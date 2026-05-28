<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
	/**
	 * Run the migration.
	 */
	public function up(): void
	{
		Schema::create('settings', static function (Blueprint $table) {
			$table->string('key', 64)->primary();
			$table->json('value');
			$table->enum('type', ['text', 'string', 'float', 'int', 'bool'])->default('text');
			$table->boolean('is_public')->default(false);
			$table->text('description')->nullable();
			$table->unsignedInteger('order')->default(0);
			$table->foreignId('author_id')->index();
			$table->timestamps();
		});

		DB::table('settings')->insert([
			[
				'key' => 'app.name',
				'value' => '"WebScore"',
				'type' => 'string',
				'is_public' => true,
				'order' => 1,
				'author_id' => 1,
				'created_at' => DB::raw('now()'),
			],
			[
				'key' => 'login.allowed',
				'value' => 'true',
				'type' => 'bool',
				'is_public' => true,
				'order' => 2,
				'author_id' => 1,
				'created_at' => DB::raw('now()'),
			],
			[
				'key' => 'register.allowed',
				'value' => 'true',
				'type' => 'bool',
				'is_public' => true,
				'order' => 3,
				'author_id' => 1,
				'created_at' => DB::raw('now()'),
			],
			[
				'key' => 'password.reset.allowed',
				'value' => 'true',
				'type' => 'bool',
				'is_public' => true,
				'order' => 4,
				'author_id' => 1,
				'created_at' => DB::raw('now()'),
			],
			[
				'key' => 'guest.upload.allowed',
				'value' => 'false',
				'type' => 'bool',
				'is_public' => true,
				'order' => 5,
				'author_id' => 1,
				'created_at' => DB::raw('now()'),
			],
		]);
	}

	/**
	 * Reverse the migration.
	 */
	public function down(): void
	{
		Schema::dropIfExists('settings');
	}
};
