<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ChatController;
use App\Http\Controllers\Api\ClasseController;
use App\Http\Controllers\Api\EcoleController;
use App\Http\Controllers\Api\UserController;
use Illuminate\Support\Facades\Route;

Route::prefix('auth')->group(function (): void {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');
    Route::get('/me', [AuthController::class, 'me'])->middleware('auth:sanctum');
});

Route::middleware('auth:sanctum')->group(function (): void {
    Route::apiResource('chats', ChatController::class);
    Route::apiResource('ecoles', EcoleController::class);
    Route::apiResource('ecoles.classes', ClasseController::class)->scoped();
    Route::apiResource('users', UserController::class);
    Route::patch('users/{user}/role', [UserController::class, 'updateRole']);
    Route::apiResource('formations', App\Http\Controllers\Api\FormationController::class);
    Route::apiResource('formations.chapitres', App\Http\Controllers\Api\ChapitreController::class)->scoped();
    Route::apiResource('chapitres.articles', App\Http\Controllers\Api\ArticleController::class)->scoped();
    Route::apiResource('progressions', App\Http\Controllers\Api\ProgressionController::class);
});
