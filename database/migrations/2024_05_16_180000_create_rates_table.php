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
		Schema::create('rates', static function (Blueprint $table) {
			$table->ulid('id')->index();
			$table->foreignUlid('score_id')->index();
			$table->foreignId('author_id')->index();
			$table->unsignedTinyInteger('rate');
			$table->string('notes', 256)->nullable();
			$table->timestamps();
			$table->primary(['score_id', 'author_id']);
		});
	}

	/**
	 * Reverse the migration.
	 */
	public function down(): void
	{
		Schema::dropIfExists('rates');
	}
};
