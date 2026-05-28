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
		Schema::create('comments', static function (Blueprint $table) {
			$table->ulid('id')->primary();
			$table->foreignId('author_id')->index();
			$table->string('object_type', 32);
			$table->foreignUlid('object_id')->nullable()->index();
			$table->text('text');
			$table->text('text_html');
			$table->unsignedInteger('number');
			$table->foreignUlid('parent_id')->nullable()->index();
			$table->foreignUlid('root_id')->nullable()->index();
			$table->timestamps();
			$table->softDeletes();
			$table->index(['object_type', 'object_id']);
		});
	}

	/**
	 * Reverse the migration.
	 */
	public function down(): void
	{
		Schema::dropIfExists('comments');
	}
};
