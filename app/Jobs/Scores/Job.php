<?php

namespace App\Jobs\Scores;

use App\Models\Enums\ScoreStatus;
use App\Models\Score;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

abstract class Job extends \App\Jobs\Job
{
	protected readonly string $filePath;

	public function __construct(protected readonly Score $score)
	{
		$this->filePath = Storage::disk($score->disk)->path($score->file);
	}

	protected function setProcessFailed(): void
	{
		$this->score->status = ScoreStatus::Failed;
		$this->score->is_valid = false;
		$this->score->process_failed_at = Carbon::now();
		$this->score->save();
	}
}
