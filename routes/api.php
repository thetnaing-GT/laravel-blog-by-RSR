<?php

use App\Http\Controllers\Api\ArticleApiController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CategoryApiController;
use App\Http\Controllers\Api\TagApiController;
use App\Http\Controllers\ArticleController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\TagController;
use Illuminate\Support\Facades\Route;

// Public routes
Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);

// Protected routes
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    
    // Articles API
    Route::apiResource('articles', ArticleApiController::class);
    Route::get('my-articles', [ArticleApiController::class, 'myArticles']);

    // Categories API
    Route::apiResource('categories', CategoryApiController::class);

    // Tags API
    Route::apiResource('tags', TagApiController::class);
});



