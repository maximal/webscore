<?php

namespace App\Jobs;

use App\Models\Enums\KnownSetting;
use App\Models\Enums\ScoreStatus;
use App\Models\Score;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Override;

class ClearGuestScoresJob extends Job
{
	#[Override]
	public function handle(): void
	{
		// Гостевой режим?
		$guest = Setting::getBool(KnownSetting::GuestUploadAllowed);
		if (!$guest) {
			return;
		}

		// Удаляем старые гостевые партитуры
		Score::query()
			->where('author_id', '=', User::GUEST_ID)
			->where('status', '=', ScoreStatus::Ready)
			->where('process_finished_at', '<', Carbon::now()->subHour())
			->orderBy('created_at')
			->offset(5)
			->orderedChunkById(100, static function (Collection $scores) {
				foreach ($scores as $score) {
					/** @var Score $score */
					Log::info('Deleting guest score #' . $score->id);
					$score->deleteFiles();
					$score->delete();
				}
			}, 'created_at');
	}
}
