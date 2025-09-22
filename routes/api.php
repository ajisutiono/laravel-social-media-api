<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\LikeController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// authentication routes
Route::post('register', [AuthController::class, 'register']);
Route::post('login', [AuthController::class, 'login']);
Route::post('logout', [AuthController::class, 'logout'])->middleware('auth:api');

// post routes
Route::prefix('posts')->middleware('auth:api')->group(function () {
    // posts
    Route::post('/', [PostController::class, 'store']);
    Route::get('/', [PostController::class, 'showAll']);
    Route::get('/{postId}', [PostController::class, 'show']);
    Route::patch('/{postId}', [PostController::class, 'update']);
    Route::delete('/{postId}', [PostController::class, 'destroy']);

    // comments
    Route::post('/{postId}/comments', [CommentController::class, 'store']);
    Route::get('/{postId}/comments', [CommentController::class, 'showAll']);
    Route::get('/{postId}/comments/{commentId}', [CommentController::class, 'show']);
    Route::patch('/{postId}/comments/{commentId}', [CommentController::class, 'update']);
    Route::delete('/{postId}/comments/{commentId}', [CommentController::class, 'destroy']);

    //   // likes (post)
    // Route::post('/{postId}/like', [LikeController::class, 'store']);
    // Route::get('/{postId}/likes', [LikeController::class, 'showAll']);
    // Route::delete('/{postId}/unlike', [LikeController::class, 'destroy']);

    // // likes (comment)
    // Route::post('/{postId}/comments/{commentId}/like', [LikeController::class, 'store']);
    // Route::get('/{postId}/comments/{commentId}/likes', [LikeController::class, 'showAll']);
    // Route::delete('/{postId}/comments/{commentId}/unlike', [LikeController::class, 'destroy']);
});

Route::middleware('auth:api')->group(function () {
    // like post
    Route::post('posts/{postId}/like', [LikeController::class, 'store']);
    Route::get('posts/{postId}/likes', [LikeController::class, 'showAll']);
    Route::delete('posts/{postId}/unlike', [LikeController::class, 'destroy']);

    // like comment
    Route::post('comments/{commentId}/like', [LikeController::class, 'store']);
    Route::get('comments/{commentId}/likes', [LikeController::class, 'showAll']);
    Route::delete('comments/{commentId}/unlike', [LikeController::class, 'destroy']);
});
