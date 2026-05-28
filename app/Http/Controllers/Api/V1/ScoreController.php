<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Requests\Api\Scores\UploadScoreRequest;
use App\Models\Score;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class ScoreController extends Controller
{
	public function create(UploadScoreRequest $request): Score
	{
		$data = $request->validated();
		if (Auth::user() === null) {
			Auth::loginUsingId(User::GUEST_ID);
		}
		return Score::createFromUploaded($data['file'], $data['title'] ?? null)->refresh();
	}

	public function read(string $id, ?string $token = null): Score
	{
		return $this->getModel($id, $token);
	}

	private function getModel(string $id, ?string $token = null): Score
	{
		$score = Score::query()->where('id', '=', $id)->first();
		abort_if(!$score, 404);
		abort_if(!$score->canBeAccessedBy(Auth::user(), $token), 404);
		return $score;
	}
}
