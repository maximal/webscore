<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Override;

/**
 * @property-read Collection<int, Score> $scores
 */
#[Fillable(['name', 'email', 'password'])]
#[Hidden(['password', 'two_factor_secret', 'two_factor_recovery_codes', 'remember_token'])]
final class User extends Authenticatable
{
	/** @use HasFactory<UserFactory> */
	use HasFactory;
	use Notifiable;
	use TwoFactorAuthenticatable;

	public const int ADMIN_ID = 1;
	public const int GUEST_ID = 2;

	#[Override]
	protected function casts(): array
	{
		return [
			'email_verified_at' => 'datetime',
			'password' => 'hashed',
			'two_factor_confirmed_at' => 'datetime',
		];
	}

	public function scores(): HasMany
	{
		return $this->hasMany(Score::class, 'author_id')
			->orderBy('created_at', 'desc');
	}

	public function event(string $type, array|object|null $data = null): void
	{
		//
	}
}
