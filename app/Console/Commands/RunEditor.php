<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class RunEditor extends Command
{
	protected $signature = 'editor:run {arguments*}';
	protected $description = 'Run MuseScore editor with specified arguments';

	/**
	 * Execute the console command.
	 */
	public function handle(): int
	{
		$arguments = $this->argument('arguments');
		$disk = Storage::disk('local');
		$path = $disk->path('squashfs-root/bin/mscore4portable');
		$escaped = [];
		foreach ($arguments as $argument) {
			$escaped[] = escapeshellarg($argument);
		}
		$fullCommand = 'DISPLAY=:2 ' . $path . ' ' . implode(' ', $escaped);
		$this->info('Running command: ' . $fullCommand);
		passthru($fullCommand, $code);
		return $code;
	}
}
