<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\PostController;
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

// Public routes (do not require authentication)
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::get('/posts', [PostController::class, 'index']); // List all posts
Route::get('/posts/{id}', [PostController::class, 'show']); // Show a single post

Route::middleware('auth:api')->group(function () {
    // Create a blog post
    Route::post('/posts', [PostController::class, 'store']);

    // Update a blog post
    Route::put('/posts/{id}', [PostController::class, 'update']);

    // Delete a blog post
    Route::delete('/posts/{id}', [PostController::class, 'destroy']);

    // Add a comment to a blog post
    Route::post('/posts/{postId}/comments', [CommentController::class, 'store']);
});
