<?php

namespace App\Jobs\Scores;

use App\Models\Enums\ProcessStage;
use App\Models\Enums\ScoreStatus;
use App\Services\Editor;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Jcupitt\Vips\Image;
use Override;
use SimpleXMLElement;

class ExportScore extends Job
{
	private const int EDITOR_DPI = 360;
	private const int IMAGE_DPI = 360;

	#[Override]
	public function handle(): void
	{
		Log::info('Score export pipeline started');

		Log::info('File path: ' . $this->filePath);
		if (!File::exists($this->filePath)) {
			Log::error('File not found');
			$this->setProcessFailed();
			return;
		}

		$score = $this->score;

		if ($this->exportMedia()) {
			$score->svg_generated_at = Carbon::now();
			$score->process_stage = ProcessStage::Mp3;
			$score->save();
		} else {
			$this->setProcessFailed();
		}

		if ($this->exportTo('mp3', true)) {
			$score->mp3_generated_at = Carbon::now();
			$score->process_stage = ProcessStage::Ogg;
			$score->save();
			if ($this->convertMp3ToOgg()) {
				$score->ogg_generated_at = Carbon::now();
				$score->process_stage = ProcessStage::Pdf;
				$score->save();
			} else {
				$this->setProcessFailed();
			}
		} else {
			$this->setProcessFailed();
		}

		if ($this->exportTo('pdf', false)) {
			$score->pdf_generated_at = Carbon::now();
			$score->process_stage = ProcessStage::Midi;
			$score->save();
		} else {
			$this->setProcessFailed();
		}

		if ($this->exportTo('midi', false)) {
			//$score->midi_generated_at = Carbon::now();
			$score->process_stage = ProcessStage::Xml;
			$score->save();
		} else {
			$this->setProcessFailed();
		}

		if ($this->exportTo('xml', false)) {
			//$score->xml_generated_at = Carbon::now();
			$score->process_stage = ProcessStage::Optimize;
			$score->save();
		} else {
			$this->setProcessFailed();
		}

		$now = Carbon::now();
		$score->status = ScoreStatus::Ready;
		$score->process_finished_at = $now;
		$score->process_stage = ProcessStage::Optimize;
		$score->save();
		$diff = $now->diffAsCarbonInterval($score->process_started_at);
		Log::info('File ready in: ' . $diff->forHumans());

		if ($score->svg_generated_at) {
			// Дополнительно запускаем оптимизацию PNG
			OptimizeImages::dispatch($score);
		}

		Log::info('Score export pipeline finished');
	}

	private function exportTo(string $extension, bool $public): bool
	{
		$extension = strtolower($extension);
		$dir = dirname($this->score->file);
		$tempFile = sys_get_temp_dir() . DIRECTORY_SEPARATOR . uniqid('score_', true) . '.' . $extension;
		if (!Editor::exportScore($this->filePath, $tempFile)) {
			return false;
		}

		if ($public) {
			$url = Storage::disk('public')->putFileAs($dir, $tempFile, 'score.' . $extension);
			if ($url) {
				Log::info('File stored publicly as: ' . $url);
			} else {
				Log::error('Cannot store file publicly');
			}
		} else {
			$url = Storage::disk($this->score->disk)->putFileAs($dir, $tempFile, 'score.' . $extension);
			if ($url) {
				Log::info('File stored privately as: ' . $url);
			} else {
				Log::error('Cannot store file privately');
			}
		}
		unlink($tempFile);

		return true;
	}

	private function convertMp3ToOgg(): bool
	{
		$publicDisk = Storage::disk('public');
		$dir = dirname($this->score->file);
		$inFile = $publicDisk->path($dir . '/score.mp3');
		$outFile = $publicDisk->path($dir . '/score.ogg');
		$command =
			'sox '
			. ' --type mp3 '
			. escapeshellarg($inFile)
			. ' --type ogg --compression 9 '
			. escapeshellarg($outFile)
			. ' 2>&1';
		Log::info('Running command: ' . $command);
		exec($command, $out, $code);
		if ($code !== 0) {
			Log::error('Exit code: ' . $code);
			Log::error('Output: ' . implode(PHP_EOL, $out));
			Log::error('Score meta error');
			return false;
		}
		return true;
	}

	private function exportMedia(): bool
	{
		$json = Editor::getScoreMedia($this->filePath, self::IMAGE_DPI);
		if (!is_object($json)) {
			return false;
		}

		// Страницы в SVG
		$pages = $json->svgs ?? [];
		if (count($pages) < 1) {
			return false;
		}
		$baseDir = dirname($this->score->file) . DIRECTORY_SEPARATOR;
		$publicDisk = Storage::disk('public');
		foreach ($pages as $page => $svg) {
			$file = $baseDir . 'page-' . ($page + 1) . '.svg';
			$processResult = $publicDisk->put($file, base64_decode($svg));
			if (!$processResult) {
				Log::error('Error writing SVG page file: ' . $file);
				return false;
			}
			Log::info('SVG stored publicly in: ' . $file);
		}

		// Эскиз файла из первой страницы в PNG
		if (count($json->pngs ?? []) > 0) {
			$preview = Image::newFromBuffer(base64_decode($json->pngs[0]))->thumbnail_image(512, [
				'size' => 'VIPS_SIZE_DOWN',
			]);
			$path = $baseDir . 'thumbnail.png';
			if ($publicDisk->put($path, $preview->writeToBuffer('.png'))) {
				$this->score->thumbnail = Storage::url($path);
			}
			$path = $baseDir . 'thumbnail.avif';
			$publicDisk->put($path, $preview->writeToBuffer('.avif', [
				'compression' => 'av1',
				'Q' => 55,
			]));

			//TODO: try {} catch {}
		}

		// Размеры страницы в миллиметрах
		$pageWidthMm = $this->score->page_width ?? $json->metadata->pageformat->width ?? 210;
		$pageHeightMm = $this->score->page_height ?? $json->metadata->pageformat->height ?? 297;

		// mm / 25.4 = inches
		// inches * DPI = pixels
		$pageWidthPx = ($pageWidthMm * self::IMAGE_DPI) / 25.4;
		$pageHeightPx = ($pageHeightMm * self::IMAGE_DPI) / 25.4;

		// Absolute pixels to relative units (0..100 %)
		$xScaleFactor = ($pageWidthPx * ((12 * self::EDITOR_DPI) / self::IMAGE_DPI)) / 100.0;
		$yScaleFactor = ($pageHeightPx * ((12 * self::EDITOR_DPI) / self::IMAGE_DPI)) / 100.0;

		// Расположения тактов по страницам
		//File::put($baseDir . 'spos.xml', base64_decode($json->sposXML));
		$xml = base64_decode($json->mposXML);
		$score = new SimpleXMLElement($xml);
		$positions = [];
		foreach ($score->elements->element as $element) {
			$attributes = $element->attributes();
			$id = (int) $attributes->id + 1;
			$positions[$id] = [
				'id' => $id,
				'x' => round((float) $attributes->x / $xScaleFactor, 3),
				'y' => round((float) $attributes->y / $yScaleFactor, 3),
				'sx' => round((float) $attributes->sx / $xScaleFactor, 3),
				'sy' => round((float) $attributes->sy / $yScaleFactor, 3),
				'page' => (int) $attributes->page + 1,
			];
		}
		foreach ($score->events->event as $event) {
			$attributes = $event->attributes();
			$id = (int) $attributes->elid + 1;
			$positions[$id]['position'] = (int) $attributes->position;
		}
		$this->score->positions = array_values($positions);

		return true;
	}
}
