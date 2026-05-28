<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Requests\Api\Scores\UploadScoreRequest;
use App\Models\Score;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class DemoController extends GuestController
{
	public function list(): array
	{
		return [];
	}

	public function create(UploadScoreRequest $request): Score
	{
		$data = $request->validated();
		Auth::loginUsingId(User::GUEST_ID);
		return Score::createFromUploaded($data['file'], $data['title'] ?? null)->refresh();
	}
}
