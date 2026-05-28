<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Process;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class InstallEditor extends Command
{
	protected $signature = 'editor:install';
	protected $description = 'Install MuseScore editor';

	public const string DOWNLOAD_URL = 'https://api.github.com/repos/musescore/MuseScore/releases/latest';

	/**
	 * Execute the console command.
	 */
	public function handle(): int
	{
		$this->info('Editor installation');
		$this->line('Finding latest version: ' . self::DOWNLOAD_URL);
		$result = Process::run('uname -m');
		$architecture = trim($result->output());
		$fileSuffix = '-' . $architecture . '.AppImage';
		$response = Http::get(self::DOWNLOAD_URL);
		$object = $response->object();
		$foundName = null;
		$foundUrl = null;
		foreach ((array) $object->assets as $asset) {
			if (Str::endsWith($asset->name, $fileSuffix)) {
				$foundName = $asset->name;
				$foundUrl = $asset->browser_download_url;
			}
		}
		if ($foundUrl === null) {
			$this->error('Could not find download URL');
			return 1;
		}

		$disk = Storage::disk('local');
		$path = $disk->path($foundName);

		if ($disk->exists($foundName)) {
			if ($this->confirm('File `' . $foundName . '` exists, download anyway?')) {
				$this->line('Downloading: ' . $foundUrl . ' → ' . $path);
				$downloadResponse = Http::timeout(0)->get($foundUrl);
				$this->line('HTTP status: ' . $downloadResponse->status());
				$disk->put($foundName, $downloadResponse->body());
			}
		}

		$this->line('Setting file rights: chmod 0755 ' . escapeshellarg($path));
		Process::run('chmod 0755 ' . escapeshellarg($path));

		$extractDirectory = 'squashfs-root';
		$disk->deleteDirectory($extractDirectory);
		$command = $path . ' --appimage-extract';
		$currentDir = getcwd();
		chdir($disk->path(''));
		$this->line('Extracting AppImage: ' . $command);
		$result = Process::run($command);
		chdir($currentDir);
		if ($result->exitCode() !== 0) {
			$this->error('Could not extract AppImage');
			$this->error(trim($result->errorOutput()));
			return $result->exitCode();
		}

		$testCommand = $disk->path($extractDirectory . '/bin/mscore4portable') . ' --long-version';
		$this->line('Test run: ' . $testCommand);
		$result = Process::run($testCommand);
		if ($result->exitCode() !== 0) {
			$this->error('Error while test run');
			$this->error(trim($result->errorOutput()));
			return $result->exitCode();
		}

		$this->info(trim($result->output()));
		$this->info('Editor installed');
		return 0;
	}
}
