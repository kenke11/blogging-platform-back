<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->group(function () {
    Route::controller(UserController::class)->group(function () {
       Route::get('/user', 'getAuthUser')->name('user.get_auth_user');
    });

    Route::controller(PostController::class)->group(function () {
        Route::post('posts', 'store')->name('posts.store');
        Route::put('posts/{post}/update', 'update')->name('posts.update');
        Route::post('posts/{post}/destroy', 'destroy')->name('posts.destroy');
        Route::post('posts/view/user/{user}/post/{post}', 'postView')->name('posts.view');
    });
});

Route::controller(AuthController::class)->group(function () {
    Route::post('login', 'login')->name('auth.login');
    Route::post('signup', 'signup')->name('auth.signup');
});

Route::get('posts', [PostController::class, 'index'])->name('posts.index');

Route::get('comments/posts/{post}', [CommentController::class, 'index'])->name('comments.index');
