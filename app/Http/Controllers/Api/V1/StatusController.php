<?php

namespace App\Http\Controllers\Api\V1;

use Illuminate\Support\Carbon;

class StatusController extends GuestController
{
	public function health(): array
	{
		return ['ok' => true, 'time' => Carbon::now()];
	}
}
