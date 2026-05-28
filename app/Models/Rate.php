<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Оценка
 *
 * @property string $score_id Идентификатор партитуры
 * @property int $rate Оценка
 * @property ?string $notes Примечания
 *
 * @property Score $score Партитура
 */
final class Rate extends Model
{
	public function score(): BelongsTo
	{
		return $this->belongsTo(Score::class);
	}
}
