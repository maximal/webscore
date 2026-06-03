<?php

namespace App\Jobs\Scores;

use App\Models\Enums\ProcessStage;
use App\Models\Enums\ScoreStatus;
use App\Services\Editor;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Process;
use Illuminate\Support\Str;
use Override;
use RuntimeException;

final class GetScoreMeta extends Job
{
	#[Override]
	public function handle(): void
	{
		Log::info('Score processing pipeline started');

		$score = $this->score;
		$score->status = ScoreStatus::Processing;
		$score->process_started_at = Carbon::now();
		$score->process_stage = ProcessStage::Meta;
		$score->save();

		Log::info('File path: ' . $this->filePath);
		if (!File::exists($this->filePath)) {
			Log::error('File not found');
			$this->setProcessFailed();
			return;
		}

		$meta = Editor::getScoreMeta($this->filePath);

		$data = $meta->metadata ?? null;
		$score->title = $data->title ?? $score->title;
		$score->slug = Str::slug(preg_replace('/\.(msc|mscz)$/i', '', $score->title));
		$score->subtitle = $data->subtitle ?? null;
		$score->composer = $data->composer ?? null;
		$score->poet = $data->poet ?? null;
		$score->duration = $data->duration ?? null;
		$score->tempo = $data->tempo ?? null;
		$score->tempo_text = $data->tempoText ?? null;
		$score->time_signature = $data->timesig ?? null;
		$score->file_version = $data->fileVersion ?? null;
		$score->has_harmonies = $data->hasHarmonies ?? null;
		$score->lyrics = $data->lyrics ?? null;
		$score->key = $data->keysig ?? null;
		$score->measures = $data->measures ?? null;
		$score->pages = $data->pages ?? null;
		$score->parts = count($data->parts ?? []);
		$score->page_width = $data->pageFormat->width ?? null;
		$score->page_height = $data->pageFormat->height ?? null;
		$score->page_twosided = $data->pageFormat->twosided ?? null;
		$score->musescore_version = $data->mscoreVersion ?? null;
		$score->musescore_processed_version = Editor::getVersion();
		$score->is_valid = $meta !== null;
		$score->meta = $meta;
		$score->meta_at = Carbon::now();
		$score->process_stage = ProcessStage::Images;
		$score->save();

		ExportScore::dispatch($score);
		Log::info('Score processing pipeline finished');
	}
}
