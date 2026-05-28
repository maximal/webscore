<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Событие системы
 *
 * @property string $type Тип события
 * @property ?string $score_id Идентификатор партитуры, связанной с событием
 * @property ?string $comment_id Идентификатор комментария, связанного с событием
 * @property ?string $rate_id Идентификатор оценки, связанной с событием
 * @property ?object $data Произвольные данные
 *
 * @property ?Score $score Партитура, связанная с событием
 * @property ?Comment $comment Комментарий, связанный с событием
 * @property ?Rate $rate Оценка, связанная с событием
 *
 */
final class Event extends Model
{
	protected $casts = ['data' => 'object'];

	public function score(): BelongsTo
	{
		return $this->belongsTo(Score::class);
	}

	public function comment(): BelongsTo
	{
		return $this->belongsTo(Comment::class);
	}

	public function rate(): BelongsTo
	{
		return $this->belongsTo(Rate::class);
	}
}
