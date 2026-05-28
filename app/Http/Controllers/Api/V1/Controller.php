<?php

namespace App\Http\Controllers\Api\V1;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;

abstract class Controller
{
	use AuthorizesRequests;
	use DispatchesJobs;
	use HasUser;
	use ValidatesRequests;
}
