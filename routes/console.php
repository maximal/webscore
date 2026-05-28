<?php

use App\Jobs\ClearGuestScoresJob;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
	$this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');


/**
 * Scheduled jobs (cron)
 */

Schedule::job(new ClearGuestScoresJob())->hourlyAt(11);
