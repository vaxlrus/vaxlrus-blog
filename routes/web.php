<?php

use App\Http\Controllers\AdminPostController;
use App\Http\Controllers\NewsletterController;
use App\Http\Controllers\PostCommentsController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\SessionsController;
use Illuminate\Support\Facades\Route;

Route::get('/', [PostController::class, 'index'])
    ->name('home');

// Posts & comments
Route::prefix('posts')->group(function() {
    Route::get('{post:slug}', [PostController::class, 'show'])
        ->name('post.show');

    Route::post('{post:slug}/comments', [PostCommentsController::class, 'store'])
        ->name('post.comment.store');

    Route::post('{post:slug/}')
        ->name('post.store');

    Route::post('{post:slug}/delete', [PostController::class, 'destroy'])
        ->name('post.delete');

    Route::delete('{post:slug}/comment/{comment:id}/delete', [PostCommentsController::class, 'destroy'])
        ->name('post.comment.delete')
        ->where('id', '[0-9]+');
});

Route::post('newsletter', NewsletterController::class);

// Registration & authorization
Route::middleware('guest')->group(function() {
    Route::prefix('register')->group(function() {
        Route::get('/', [RegisterController::class, 'create']);
        Route::post('/', [RegisterController::class, 'store']);
    });

    Route::prefix('login')->group(function() {
        Route::get('/', [SessionsController::class, 'create']);
        Route::post('/', [SessionsController::class, 'store']);
    });
});

Route::middleware('auth')->group(function() {
    Route::post('logout', [SessionsController::class, 'destroy']);
});

// Admin Section
Route::middleware('can:admin')->group(function () {
    Route::resource('admin/posts', AdminPostController::class)->except('show');
});
