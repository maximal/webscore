<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
	/**
	 * Run the migrations.
	 */
	public function up(): void
	{
		Schema::create('users', static function (Blueprint $table) {
			$table->id();
			$table->string('name');
			$table->string('username', 32)->nullable()->index();
			$table->string('email')->index();
			//
			$table->bigInteger('inviter_id')->index()->nullable();
			$table->string('normalized_email')->nullable()->index();
			$table->timestampTz('email_verified_at')->nullable();
			//
			$table->string('password');
			$table->text('two_factor_secret')->nullable();
			$table->text('two_factor_recovery_codes')->nullable();
			$table->timestampTz('two_factor_confirmed_at')->nullable();
			$table->rememberToken();
			//
			$table->timestampsTz();
		});

		Schema::create('password_reset_tokens', static function (Blueprint $table) {
			$table->string('email')->primary();
			$table->string('token');
			$table->timestampTz('created_at')->nullable();
		});

		Schema::create('sessions', static function (Blueprint $table) {
			$table->string('id')->primary();
			$table->foreignId('user_id')->nullable()->index();
			$table->string('ip_address', 45)->nullable();
			$table->text('user_agent')->nullable();
			$table->longText('payload');
			$table->integer('last_activity')->index();
		});
	}

	/**
	 * Reverse the migrations.
	 */
	public function down(): void
	{
		Schema::dropIfExists('users');
		Schema::dropIfExists('password_reset_tokens');
		Schema::dropIfExists('sessions');
	}
};
