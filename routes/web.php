<?php

use App\Http\Controllers\ScoreController;
use App\Http\Controllers\WelcomeController;
use Illuminate\Support\Facades\Route;

Route::get('/', [WelcomeController::class, 'index'])->name('index');

Route::get('/score/new', [ScoreController::class, 'create'])->name('score.new');
Route::get('/score/{id}/download.{format}', [ScoreController::class, 'download'])->name(
	'score.download',
);
Route::get('/score/{id}/{slug?}/{token?}', [ScoreController::class, 'view'])->name('score');
Route::get('/scores/{id}/{slug?}/{token?}', [ScoreController::class, 'view']);


Route::middleware(['auth', 'verified'])->group(function () {
	//
	Route::inertia('dashboard', 'Dashboard')->name('dashboard');

	//	Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
	//	Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
	//	Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
	//
	//	Route::get('/my/scores', [MyController::class, 'scores'])->name('my.scores');
	//
	//	Route::prefix('/admin')->group(function () {
	//		Route::get('/', [AdminController::class, 'index'])->name('admin');
	//		Route::get('/settings', [AdminController::class, 'settings'])->name('admin.settings');
	//		Route::get('/users', [AdminController::class, 'users'])->name('admin.users');
	//	});
});

require __DIR__ . '/settings.php';
