<?php

use App\Http\Controllers\Api\V1\CommentController;
use App\Http\Controllers\Api\V1\DemoController;
use App\Http\Controllers\Api\V1\ScoreController;
use App\Http\Controllers\Api\V1\StatusController;
use App\Http\Controllers\Api\V1\UserController;

Route::prefix('/v1')->group(static function () {
	Route::get('/health', [StatusController::class, 'health']);

	Route::prefix('/demo')->group(static function () {
		Route::get('/', [DemoController::class, 'list']);
		Route::post('/', [DemoController::class, 'create']);

		Route::prefix('/{id}')->group(static function () {
			Route::get('/', [ScoreController::class, 'read']);
		});
	});

	Route::middleware('auth:api')->group(static function () {
		Route::prefix('/user')->group(static function () {
			Route::get('/', [UserController::class, 'read']);
		});
		Route::prefix('/scores')->group(static function () {
			//
			Route::prefix('/{id}')->group(static function () {
				Route::get('/', [ScoreController::class, 'read']);
			});
		});

		// Комментарии
		Route::prefix('/comments')->group(static function () {
			Route::get('/', [CommentController::class, 'list'])
				->withoutMiddleware('auth:api');
			Route::post('/', [CommentController::class, 'create']);
			Route::prefix('/{id}')->group(static function () {
				Route::put('/', [CommentController::class, 'update']);
				Route::get('/', [CommentController::class, 'get']);
				Route::delete('/', [CommentController::class, 'delete']);
				Route::post('/restore', [CommentController::class, 'restore']);
			});
		});
	});
});
