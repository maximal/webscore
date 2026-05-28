<?php
/**
 *
 * @author MaximAL
 * @since 2020-10-19
 * @date 2020-10-19
 * @time 13:52
 * @copyright © MaximAL, Sijeko 2020
 */

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

abstract class Job implements ShouldQueue
{
	use Dispatchable;
	use InteractsWithQueue;
	use Queueable;
	use SerializesModels;

	/**
	 * Execute the job.
	 */
	abstract public function handle(): void;
}
