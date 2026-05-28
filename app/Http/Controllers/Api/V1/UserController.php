<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\User;

class UserController extends Controller
{
	/**
	 * Получить информацию о текущем залогиненном пользователе.
	 */
	public function read(): User
	{
		return $this->user();
	}
}
