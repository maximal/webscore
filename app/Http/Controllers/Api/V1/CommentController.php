<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\Comment;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

class CommentController extends Controller
{
	public function list(Request $request): Collection
	{
		$data = $request->validate([
			'type' => 'required|in:score',
			'id' => 'required|bail|ulid',
		]);
		return Comment::query()
			->where('object_type', '=', $data['type'])
			->where('object_id', '=', $data['id'])
			->orderBy('created_at', 'asc')
			->with('author')
			->get();
	}

	public function create(Request $request): Comment
	{
		$data = $request->validate([
			'object_type' => 'required|in:score',
			'object_id' => 'required|bail|ulid',
			//'parent_id' => 'required|bail|ulid',
			'text' => 'required|max:500',
		]);
		$model = new Comment();
		$model->forceFill($data);
		$model->number =
			(
				Comment::query()
					->where('object_type', '=', $model->object_type)
					->where('object_id', '=', $model->object_id)
					->withTrashed()
					->max('number') ?? 0
			) + 1;
		abort_if(!$model->save(), 'Unable to create comment');
		return $model->load('author')->refresh();
	}

	public function delete(string $id): Response
	{
		/** @var ?Comment $model */
		$model = Comment::where('id', '=', $id)->where('author_id', '=', Auth::id())->first();
		abort_if(!$model, 404, 'Comment not found: #' . $id);
		abort_if(!$model->delete(), 500, 'Unable to delete comment: #' . $id);
		return new Response('', 204);
	}
}
