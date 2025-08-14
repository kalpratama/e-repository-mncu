<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ArticleController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DocumentTypeController;
use App\Http\Controllers\CategoryController;
use App\Http\Middleware\IsAdmin;
use SebastianBergmann\CodeCoverage\Report\Html\Dashboard;
use App\Http\Controllers\API\BackgroundImageController;


Route::get('/dashboard-data', [DashboardController::class, 'index']);
Route::get('/search', [DashboardController::class, 'search']);
Route::get('/document-types', [DocumentTypeController::class, 'index']);

Route::get('/articles/{document}', [ArticleController::class, 'show']);
// Route::get('/category/all', [CategoryController::class, 'show']);
Route::get('/category/{slug}', [CategoryController::class, 'show']);

Route::get('/background-images', [BackgroundImageController::class, 'index']);


Route::post('/login', [AuthController::class, 'login']);
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', [AuthController::class, 'user']);

    Route::post('/articles', [ArticleController::class, 'store'])
        ->middleware(IsAdmin::class); // Ensure only admins can create articles

    Route::get('/articles/{document}/download', [ArticleController::class, 'download']);
    Route::put('/articles/{document}', [ArticleController::class, 'update'])->middleware(IsAdmin::class);
    Route::delete('/articles/{document}', [ArticleController::class, 'destroy'])->middleware(IsAdmin::class);

    Route::get('/admin/background-images', [BackgroundImageController::class, 'adminIndex']);
    Route::post('/admin/background-images', [BackgroundImageController::class, 'store']);
    Route::patch('/admin/background-images/{backgroundImage}', [BackgroundImageController::class, 'update']);
    Route::delete('/admin/background-images/{backgroundImage}', [BackgroundImageController::class, 'destroy']);
    Route::post('/admin/background-images/update-order', [BackgroundImageController::class, 'updateOrder']);

});