<?php

namespace App\Http\Controllers;

use App\Models\Enums\ScoreAccess;
use App\Models\Enums\ScoreStatus;
use App\Models\Score;
use App\Models\Setting;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class WelcomeController extends Controller
{
	public function index(): Response
	{
		$query = Score::query()
			->where('status', '=', ScoreStatus::Ready)
			->orderBy('created_at', 'desc');

		// Гости видят только публичные объекты
		if (Auth::guest()) {
			$query->where('access', '=', ScoreAccess::Public);
		} else {
			$query->whereIn('access', [ScoreAccess::Public, ScoreAccess::Registered]);
		}

		$paginator = $query->paginate(24);

		return Inertia::render('IndexPage', [
			'models' => $paginator->items(),
			'currentPage' => $paginator->currentPage(),
			'lastPage' => $paginator->lastPage(),
			'perPage' => $paginator->perPage(),
			'total' => $paginator->total(),
			'canLogin' => Setting::get('login.allowed', false),
			'canRegister' => Setting::get('register.allowed', false),
		]);
	}
}
