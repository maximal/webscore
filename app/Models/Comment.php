<?php

namespace App\Models;

use App\Models\Traits\HasAuthor;
use App\Tools\Markdown;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

/**
 * Комментарий
 *
 * @property string $object_type Тип объекта
 * @property string $object_id Идентификатор объекта
 * @property string $text Текст комментария в Маркдауне
 * @property string $text_html Текст комментария в HTML-формате
 * @property int $number Номер комментария (уникален в пределах объекта)
 * @property string $parent_id Идентификатор родительского комментария в обсуждении
 * @property string $root_id Идентификатор корневого комментария в обсуждении (ветка дерева)
 * @property ?Carbon $deleted_at Таймштамп удаления
 *
 * @property Score|Model $object
 * @property ?Comment $parent
 * @property ?Comment $root
 * @property Comment[] $replies
 * @property Comment[] $thread
 */
final class Comment extends Model
{
	use SoftDeletes;
	use HasAuthor;

	protected $hidden = ['deleted_at'];

	protected static function booted(): void
	{
		parent::booted();
		self::creating(static function (self $model) {
			$model->text_html = Markdown::of($model->text, $model->object);
			if ($model->parent) {
				$model->root_id = $model->parent->root_id ?? $model->parent_id;
			}
		});
		self::updating(static function (self $model) {
			$model->text_html = Markdown::of($model->text, $model->object);
		});
		self::created(static function (self $model) {
			$model->updateObjectAggregates();
		});
		self::updated(static function (self $model) {
			$model->updateObjectAggregates();
		});
		self::deleted(static function (self $model) {
			$model->updateObjectAggregates();
		});
	}

	public function object(): MorphTo
	{
		return $this->morphTo();
	}

	public function parent(): BelongsTo
	{
		return $this->belongsTo(self::class);
	}

	public function root(): BelongsTo
	{
		return $this->belongsTo(self::class);
	}

	public function replies(): HasMany
	{
		return $this->hasMany(self::class, 'parent_id')
			->orderBy('created_at');
	}

	public function thread(): HasMany
	{
		return $this->hasMany(self::class, 'root_id')
			->orderBy('created_at');
	}

	private function updateObjectAggregates(): void
	{
		if ($this->object->hasAttribute('comments_count')) {
			$this->object->comments_count = self::query()
				->where('object_type', '=', $this->object_type)
				->where('object_id', '=', $this->object_id)
				->count();
			$this->object->save();
		}
	}
}
