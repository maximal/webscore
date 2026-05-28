<?php

namespace App\Models;

use App\Models\Traits\HasAuthor;
use Carbon\CarbonImmutable;
use DateTimeImmutable;
use DateTimeInterface;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use Override;

/**
 * Базовая модель
 *
 * @property string $id УУИД
 * @property ?Carbon $created_at Таймштамп создания
 * @property ?Carbon $updated_at Таймштамп обновления
 */
abstract class Model extends \Illuminate\Database\Eloquent\Model
{
	use HasAuthor;
	use HasUlids;

	// Секунды: `Y-m-d\TH:i:sp`
	// Миллисекунды: `Y-m-d\TH:i:s.vp`
	// Микросекунды: `Y-m-d\TH:i:s.up`, не показываем микросекунды
	protected $dateFormat = 'Y-m-d\TH:i:sp';

	#[Override]
	protected function serializeDate(DateTimeInterface $date): string
	{
		return $date instanceof DateTimeImmutable ?
			CarbonImmutable::instance($date)->format($this->dateFormat) :
			Carbon::instance($date)->format($this->dateFormat);
	}

	public static function newId(): string
	{
		return Str::lower(Str::ulid()->toString());
	}

	public static function findById(string|int|null $id, array $with = []): ?static
	{
		// Не менять `static` на `self`!
		// Здесь нужно именно позднее связывание.
		return $id ? static::where('id', '=', $id)->with($with)->first() : null;
	}
}
