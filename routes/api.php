<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\PostController;
use App\Http\Middleware\LogApiRequests;
use Illuminate\Support\Facades\Route;


Route::middleware(LogApiRequests::class)->group(function () {

    
    Route::post('/login', [AuthController::class, 'login'])
        ->middleware('throttle:10,1')
        ->name('api.login');

    
    Route::middleware('auth:sanctum')->group(function () {
        Route::get('/me',           [AuthController::class, 'me'])->name('api.me');
        Route::post('/logout',      [AuthController::class, 'logout'])->name('api.logout');
        Route::get('/posts',        [PostController::class, 'index'])->name('api.posts.index');
        Route::get('/posts/{slug}', [PostController::class, 'show'])->name('api.posts.show');
        Route::get('/categories',   [CategoryController::class, 'index'])->name('api.categories.index');
    });
});