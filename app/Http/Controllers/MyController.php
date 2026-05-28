<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class MyController extends Controller
{
	public function scores(): Response
	{
		return Inertia::render('scores/MyScores', [
			'models' => Auth::user()->scores,
		]);
	}
}
