<?php

namespace App\Models\Traits;

use App\Models\User;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Auth;

/**
 * @property int $author_id ID автора
 * @property User $author Автор
 */
trait HasAuthor
{
	/**
	 * The "booted" method of the model.
	 */
	protected static function bootHasAuthor(): void
	{
		static::creating(static function (self $model) {
			if (!$model->author_id) {
				$model->author_id = Auth::id();
			}
		});
		static::updating(static function (self $model) {
			if (!$model->author_id) {
				$model->author_id = Auth::id();
			}
		});
	}

	/**
	 * Автор
	 */
	public function author(): BelongsTo
	{
		return $this->belongsTo(User::class, 'author_id');
	}
}
