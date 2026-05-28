<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\User;
use Illuminate\Support\Facades\Auth;

trait HasUser
{
	/**
	 * Текущий залогиненный пользователь
	 */
	public function user(): User
	{
		return Auth::user();
	}
}
