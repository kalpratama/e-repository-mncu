<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ArticleController;
use App\Http\Controllers\DashboardController;
use App\Http\Middleware\IsAdmin;
use SebastianBergmann\CodeCoverage\Report\Html\Dashboard;

Route::get('/dashboard-data', [DashboardController::class, 'index']);
Route::get('/search', [DashboardController::class, 'search']);

Route::post('/login', [AuthController::class, 'login']);
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', [AuthController::class, 'user']);

    Route::post('/articles', [ArticleController::class, 'store'])
        ->middleware(IsAdmin::class); // Ensure only admins can create articles
});