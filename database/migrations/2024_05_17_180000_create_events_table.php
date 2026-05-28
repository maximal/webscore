<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
	/**
	 * Run the migration.
	 */
	public function up(): void
	{
		Schema::create('events', static function (Blueprint $table) {
			$table->ulid('id')->primary();
			$table->foreignId('author_id')->index();
			$table->string('type', 64);
			$table->foreignUlid('score_id')->nullable()->index();
			$table->foreignUlid('comment_id')->nullable()->index();
			$table->foreignUlid('rate_id')->nullable()->index();
			$table->json('data')->nullable();
			$table->timestamps();
		});
	}

	/**
	 * Reverse the migration.
	 */
	public function down(): void
	{
		Schema::dropIfExists('events');
	}
};
