<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

//use Illuminate\Database\Console\Seeds\WithoutModelEvents;

final class DatabaseSeeder extends Seeder
{
	/**
	 * Seed the application's database.
	 */
	public function run(): void
	{
		// User::factory(10)->create();
		DB::table('users')->insert([
			[
				'name' => 'Test Admin',
				'username' => 'admin',
				'email' => 'almaximal@ya.ru',
				'email_verified_at' => DB::raw('now()'),
				//'role' => 'admin',
				'inviter_id' => null,
				'normalized_email' => 'almaximal@ya.ru',
				'password' => Hash::make('testadminpass'),
				'created_at' => DB::raw('now()'),
			],
			[
				'name' => 'Guest User',
				'username' => 'guest',
				'email' => 'almaximal+guest@ya.ru',
				'email_verified_at' => DB::raw('now()'),
				//'role' => 'admin',
				'inviter_id' => 1,
				'normalized_email' => 'almaximal+guest@ya.ru',
				'password' => Hash::make('testpass'),
				'created_at' => DB::raw('now()'),
			],
			[
				'name' => 'Test User',
				'username' => 'user',
				'email' => 'almaximal+user@ya.ru',
				'email_verified_at' => DB::raw('now()'),
				//'role' => null,
				'inviter_id' => 1,
				'normalized_email' => 'almaximal+user@ya.ru',
				'password' => Hash::make('testuserpass'),
				'created_at' => DB::raw('now()'),
			],
			[
				'name' => 'Test User 2',
				'username' => 'root',
				'email' => 'almaximal+user2@ya.ru',
				'email_verified_at' => DB::raw('now()'),
				//'role' => null,
				'inviter_id' => 1,
				'normalized_email' => 'almaximal+user2@ya.ru',
				'password' => Hash::make('testuser2pass'),
				'created_at' => DB::raw('now()'),
			],
		]);
	}
}
