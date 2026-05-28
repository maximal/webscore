<?php

namespace App\Http\Controllers;

use App\Models\Enums\ScoreStatus;
use App\Models\Score;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ScoreController extends Controller
{
	public function create(): Response
	{
		return Inertia::render('scores/NewScore');
	}

	public function view(
		string $id,
		?string $slug = null,
		?string $token = null,
	): Response|RedirectResponse {
		$model = $this->getModel($id, $token);

		if ($slug !== $model->slug) {
			return redirect()->route('score', [
				'id' => $model->id,
				'slug' => $model->slug,
			]);
		}

		if ($model->status !== ScoreStatus::Ready) {
			return Inertia::render('scores/ScoreNotReady', [
				'model' => $model,
				'token' => $token,
				'isAuthor' => $model->author_id === Auth::id(),
			]);
		}

		return Inertia::render('scores/ScoreView', [
			'model' => $model,
			'token' => $token,
			'isAuthor' => $model->author_id === Auth::id(),
		]);
	}

	public function download(string $id, string $format, ?string $token = null): StreamedResponse
	{
		$score = $this->getModel($id, $token);

		// Проверка доступа
		$format = strtolower($format);
		abort_if(!$score->canBeDownloadedBy(Auth::user(), $format, $token), 404);

		switch ($format) {
			case 'ogg':
			case 'mp3':
				// Download Audio
				$publicDisk = Storage::disk($score->disk_public);
				if ($publicDisk->exists($score->dir . '/score.' . $format)) {
					return $publicDisk->download(
						$score->dir . '/score.' . $format,
						$score->title . '.' . $format,
					);
				}
			break;

			case 'mscz':
			case 'msc':
				// Download MuseScore Source
				$privateDisk = Storage::disk($score->disk);
				if ($privateDisk->exists($score->dir . '/score.mscz')) {
					return $privateDisk->download(
						$score->dir . '/score.mscz',
						$score->title . '.mscz',
					);
				}
				if ($privateDisk->exists($score->dir . '/score.msc')) {
					return $privateDisk->download(
						$score->dir . '/score.msc',
						$score->title . '.msc',
					);
				}
				break;

			case 'pdf':
			case 'xml':
			case 'midi':
				// Download Other Formats
				$privateDisk = Storage::disk($score->disk);
				if ($privateDisk->exists($score->dir . '/score.' . $format)) {
					return $privateDisk->download(
						$score->dir . '/score.' . $format,
						$score->title . '.' . $format,
					);
				}
				break;
		}
		abort(404);
	}

	private function getModel(string $id, ?string $token = null): Score
	{
		$score = Score::query()->where('id', '=', $id)->first();
		abort_if(!$score, 404);
		abort_if(!$score->canBeAccessedBy(Auth::user(), $token), 404);
		return $score;
	}
}
