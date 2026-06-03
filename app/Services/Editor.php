<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Process;
use Throwable;

final class Editor
{
	private const string BASE = 'mscore';

	public static function getScoreMeta(string $file): ?object
	{
		$result = self::json(self::BASE . ' --score-meta ' . escapeshellarg($file));
		if ($result !== null) {
			return $result;
		}
		Log::warning('File invalid, trying `force` mode...');
		return self::json(self::BASE . ' --force --score-meta ' . escapeshellarg($file));
	}

	public static function getScoreMedia(string $file, int $dpi = 360): ?object
	{
		$result = self::json(
			self::BASE . ' --force --image-resolution ' . $dpi . ' --score-media ' . escapeshellarg($file),
		);
		if (!is_object($result)) {
			return null;
		}
		return $result;
	}

	public static function exportScore(string $file, string $out): bool
	{
		$extension = strtolower(pathinfo($out, PATHINFO_EXTENSION));
		if (!in_array($extension, ['mp3', 'pdf', 'midi', 'xml'])) {
			Log::error('Invalid file type: ' . $extension);
			return false;
		}
		$result = self::output(
			self::BASE
				. ' --force --export-to '
				. escapeshellarg($out)
				. ' --bitrate 256'
				. ' --image-resolution 360 '
				. ' '
				. escapeshellarg($file),
		);
		return $result !== null;
	}

	public static function getVersion(): ?string
	{
		$output = self::output(self::BASE . ' --long-version') ?? '';
		if (!preg_match('/Version\s*([\d.]+);\s*Build(\s+([0-9a-f]+))?/i', $output, $match)) {
			return null;
		}
		// version + build hash
		// or
		// version
		return isset($match[3]) ? ($match[1] . '+' . $match[3]) : $match[1];
	}

	private static function json(string $command): ?object
	{
		[$code, $output, $error] = self::result($command);
		if ($code !== 0) {
			Log::error('Error executing command: ' . $command);
			Log::error('STDOUT: ' . $output);
			Log::error('STDERR: ' . $error);
			return null;
		}
		try {
			$object = json_decode($output, false, 512, JSON_THROW_ON_ERROR);
		} catch (Throwable $exception) {
			Log::error('Error parsing output of command: ' . $command);
			Log::error('Exception: ' . $exception->getMessage());
			Log::error('STDOUT:    ' . $output);
			Log::error('STDERR:    ' . $error);
			return null;
		}
		if (!is_object($object)) {
			Log::error('Parsed output is not an object in command: ' . $command);
			Log::error('STDOUT: ' . $output);
			Log::error('STDERR: ' . $error);
			return null;
		}
		return $object;
	}

	private static function output(string $command): ?string
	{
		[$code, $output, $error] = self::result($command);
		if ($code !== 0) {
			Log::error('Error executing command: ' . $command);
			Log::error('STDOUT: ' . $output);
			Log::error('STDERR: ' . $error);
		}
		return $output;
	}

	private static function result(string $command): array
	{
		$full = 'xvfb-run -a ' . $command;
		$timeout = config('editor.timeout', 120);
		Log::debug('Running command (with timeout ' . $timeout . '): ' . $full);
		$result = Process::timeout($timeout)->run($full);
		return [$result->exitCode(), $result->output(), $result->errorOutput()];
	}
}
