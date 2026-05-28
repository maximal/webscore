<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
	public const array ACCESS_TYPE = ['public', 'link', 'private'];

	/**
	 * Run the migration.
	 */
	public function up(): void
	{
		Schema::create('scores', static function (Blueprint $table) {
			$table->ulid('id')->primary();
			$table->string('title', 120);
			$table->string('file');
			$table->string('disk', 64);
			$table->string('disk_public', 64);
			$table->foreignId('author_id')->index();
			$table->string('slug', 120);
			$table->enum(
				'status',
				['created', 'processing', 'ready', 'invalid', 'failed']
			)->default('created');
			// Score settings
			$table->enum('access', self::ACCESS_TYPE)->default('public');
			$table->enum('msc_download', self::ACCESS_TYPE)->default('public');
			$table->enum('pdf_download', self::ACCESS_TYPE)->default('public');
			$table->enum('xml_download', self::ACCESS_TYPE)->default('public');
			$table->enum('midi_download', self::ACCESS_TYPE)->default('public');
			$table->string('access_token', 32);
			$table->boolean('comments_shown')->default(true);
			$table->boolean('comments_enabled')->default(true);
			$table->text('text')->nullable();
			$table->text('text_html')->nullable();
			//
			$table->string('thumbnail', 128)->nullable();
			// File meta
			$table->string('subtitle', 250)->nullable();
			$table->string('composer', 250)->nullable();
			$table->string('poet', 250)->nullable();
			$table->unsignedSmallInteger('duration')->nullable();
			$table->unsignedSmallInteger('tempo')->nullable();
			$table->string('tempo_text', 250)->nullable();
			$table->string('time_signature', 16)->nullable();
			$table->unsignedSmallInteger('file_version')->nullable();
			$table->boolean('has_harmonies')->nullable();
			$table->text('lyrics')->nullable();
			$table->tinyInteger('key')->nullable();
			$table->unsignedSmallInteger('measures')->nullable();
			$table->unsignedSmallInteger('pages')->nullable();
			$table->unsignedSmallInteger('parts')->nullable();
			$table->unsignedSmallInteger('page_height')->nullable();
			$table->unsignedSmallInteger('page_width')->nullable();
			$table->boolean('page_twosided')->nullable();
			$table->boolean('is_valid')->nullable();
			$table->string('musescore_version', 32)->nullable();
			$table->string('musescore_processed_version', 32)->nullable();
			$table->json('meta')->nullable();
			$table->json('positions')->nullable();
			$table->unsignedInteger('total_size');
			$table->unsignedInteger('file_size');
			$table->char('file_hash', 64)->index();
			$table->enum(
				'process_stage',
				['waiting', 'meta', 'images', 'mp3', 'ogg', 'pdf', 'midi', 'xml', 'optimize']
			)->nullable();
			// Linked aggregates
			$table->unsignedInteger('comments_count')->default(0);
			$table->unsignedInteger('rates_count')->default(0);
			$table->double('rate_average')->nullable();
			// Timestamps
			$table->timestamp('process_started_at')->nullable();
			$table->timestamp('process_finished_at')->nullable();
			$table->timestamp('process_failed_at')->nullable();
			$table->timestamp('meta_at')->nullable();
			$table->timestamp('svg_generated_at')->nullable();
			$table->timestamp('mp3_generated_at')->nullable();
			$table->timestamp('ogg_generated_at')->nullable();
			$table->timestamp('pdf_generated_at')->nullable();
			$table->timestamps();
			$table->softDeletes();
		});
	}

	/**
	 * Reverse the migration.
	 */
	public function down(): void
	{
		Schema::dropIfExists('scores');
	}
};
