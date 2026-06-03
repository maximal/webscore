<?php

namespace App\Console\Commands;

use App\Jobs\Scores\GetScoreMeta;
use App\Models\Score;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class RebuildScore extends Command
{
	protected $signature = 'score:rebuild {id}';
	protected $description = 'Rebuild score with given ID';

	public function handle(): int
	{
		$id = (string) $this->argument('id');
		$score = Score::query()->where('id', '=', $id)->first();
		if (!$score) {
			$this->error('Score not found #' . $id);
			return self::INVALID;
		}

		$disk = Storage::disk($score->disk);
		$file = $score->file;
		$contents = $disk->get($file);
		if ($contents === '' || $contents === null) {
			$this->error('Score file not found in disk `' . $score->disk . '`: ' . $file);
			return self::INVALID;
		}
		$this->info('Deleting score files...');
		$score->deleteFiles();
		$disk->put($file, $contents);
		$this->info('Dispatching processing pipeline...');
		GetScoreMeta::dispatch($score);
		return self::SUCCESS;
	}
}
