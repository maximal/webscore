<?php

namespace App\Models;

use App\Models\Enums\KnownSetting;
use App\Models\Enums\SettingType;
use App\Models\Traits\HasAuthor;
use Illuminate\Database\Eloquent\Attributes\WithoutIncrementing;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

/**
 * Настройка системы
 *
 * @property string $key
 * @property string|float|int|bool $value
 * @property SettingType $type
 * @property bool $is_public
 * @property ?string $description
 * @property int<0,max> $order
 */
#[WithoutIncrementing]
final class Setting extends Model
{
	use HasAuthor;

	protected $casts = [
		'value' => 'object',
		'type' => SettingType::class,
		'is_public' => 'boolean',
	];
	protected $primaryKey = 'key';
	protected $keyType = 'string';
	public $incrementing = false;

	public const string CACHE_PREFIX = 'settings:';
	public const int CACHE_TTL = 60 * 60;

	protected static function booted(): void
	{
		parent::booted();
		self::created(static function (self $model) {
			$model->invalidateCache();
		});
		self::updated(static function (self $model) {
			$model->invalidateCache();
		});
		self::deleted(static function (self $model) {
			$model->invalidateCache();
		});
	}

	public static function get(
		string|KnownSetting $key,
		bool|float|int|string $default,
	): bool|float|int|string {
		$strKey = $key instanceof KnownSetting ? $key->value : $key;
		$value = Cache::get(self::CACHE_PREFIX . $strKey);
		if ($value === null) {
			$model = self::query()->where('key', '=', $strKey)->first();
			$value = $model !== null ? $model->value : $default;
			Cache::set(self::CACHE_PREFIX . $strKey, $value, self::CACHE_TTL);
		}
		return $value;
	}

	public static function set(
		string|KnownSetting $key,
		bool|float|int|string $value,
		SettingType $type,
	): void {
		$strKey = $key instanceof KnownSetting ? $key->value : $key;
		$model = self::query()->where('key', '=', $strKey)->first();
		if ($model === null) {
			$model = new self();
			$model->key = $strKey;
			$model->type = $type;
		}
		$model->value = $value;
		$model->save();
		Cache::set(self::CACHE_PREFIX . $strKey, $value, self::CACHE_TTL);
	}

	public static function getPublicSettings(): array
	{
		return (array) Cache::remember(
			self::CACHE_PREFIX,
			self::CACHE_TTL,
			static function (): array {
				/** @var Collection<int,self> $settings */
				$settings = self::query()
					->select(['key', 'value', 'type'])
					->where('is_public', '=', true)
					//->orderBy('order')
					->get();
				$result = [];
				foreach ($settings as $setting) {
					$result[$setting->key] = [
						'key' => $setting->key,
						'value' => $setting->getTypedValue(),
						'type' => $setting->type->value,
					];
				}
				return $result;
			},
		);
	}

	public static function getText(string|KnownSetting $key, string $default = ''): string
	{
		return (string) self::get($key, $default);
	}

	public static function getString(string|KnownSetting $key, string $default = ''): string
	{
		return preg_replace('/[\r\n]+/', ' ', self::getText($key, $default));
	}

	public static function getFloat(string|KnownSetting $key, float $default = 0.0): float
	{
		return (float) self::get($key, $default);
	}

	public static function getInt(string|KnownSetting $key, int $default = 0): int
	{
		return (int) self::get($key, $default);
	}

	public static function getBool(string|KnownSetting $key, bool $default = false): bool
	{
		return (bool) self::get($key, $default);
	}

	/** @mutation-free */
	private function getTypedValue(): string|float|int|bool
	{
		return match ($this->type) {
			SettingType::Text => (string) $this->value,
			SettingType::String => preg_replace('/[\r\n]+/', ' ', (string) $this->value),
			SettingType::Float => (float) $this->value,
			SettingType::Int => (int) $this->value,
			SettingType::Bool => (bool) $this->value,
		};
	}

	private function invalidateCache(): void
	{
		Cache::delete(self::CACHE_PREFIX);
		Cache::delete(self::CACHE_PREFIX . $this->key);
	}
}
