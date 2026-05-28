<?php

namespace App\Jobs\Scores;

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Process;
use Illuminate\Support\Facades\Storage;

//use Jcupitt\Vips\Image;

class OptimizeImages extends Job
{
	public function handle(): void
	{
		Log::info(__CLASS__ . ' started');
		$score = $this->score;
		if (!$score->thumbnail) {
			Log::error('No thumbnail PNG');
			return;
		}
		$publicDisk = Storage::disk('public');
		$thumbnailUrl = dirname($score->file) . '/thumbnail.png';
		$thumbnailPath = $publicDisk->path($thumbnailUrl);
		Log::info('Thumbnail PNG: ' . $thumbnailPath);
		if (!$publicDisk->exists($thumbnailUrl)) {
			Log::error('File not found');
			return;
		}
		$process = Process::run('oxipng --opt=max --strip=safe -vvv ' . escapeshellarg($thumbnailPath));
		[$code, $stdout, $stderr] = [
			(int) $process->exitCode(),
			$process->output(),
			$process->errorOutput(),
		];

		if ($code !== 0) {
			Log::warning('Could not optimize PNG: ' . $thumbnailPath);
			Log::warning('Result code: ' . $code);
			Log::warning('STDOUT:      ' . $stdout);
			Log::warning('STDERR:      ' . $stderr);
		}
		//$avifPath = preg_replace('/\.png$/', '-1.avif', $fullPath);
		//Image::newFromFile($fullPath)->writeToFile($avifPath, ['compression' => 'av1', 'Q' => 55]);
		$score->process_stage = null;
		$score->total_size = $this->calculateTotalSize();
		$score->save();

		Log::info(__CLASS__ . ' finished');
		$diff = Carbon::now()->diffAsCarbonInterval($score->process_started_at);
		Log::info('Score processing pipeline finished in ' . $diff->forHumans());
	}

	private function calculateTotalSize(): int
	{
		$score = $this->score;
		$dir = dirname($score->file);

		$disk = Storage::disk($score->disk);
		$total = $disk->size($score->file);
		$total += $disk->size($dir . '/score.midi');
		$total += $disk->size($dir . '/score.pdf');
		$total += $disk->size($dir . '/score.xml');

		$publicDisk = Storage::disk('public');
		$total += $publicDisk->size($dir . '/score.mp3');
		$total += $publicDisk->size($dir . '/score.ogg');
		$total += $publicDisk->size($dir . '/thumbnail.png');
		$total += $publicDisk->size($dir . '/thumbnail.avif');
		for ($page = 1; $page <= $score->pages; $page++) {
			$total += $publicDisk->size($dir . '/page-' . $page . '.svg');
		}

		return $total;
	}
}
