<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ArticleController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DocumentTypeController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\UsersController;
use App\Http\Middleware\IsAdmin;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
// use App\Http\Controllers\API\BackgroundImageController;

// public Routes
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/debug/send-otp', [AuthController::class, 'debugSendOTP']);
Route::delete('/cleanup-unverified', [AuthController::class, 'cleanupUnverified']);

// Articles and related routes
Route::get('/dashboard-data', [DashboardController::class, 'index']);
Route::get('/search', [DashboardController::class, 'search']);    Route::get('/document-types', [DocumentTypeController::class, 'index']);
Route::get('/articles/{document}', [ArticleController::class, 'show']);
Route::get('/category/{slug}', [CategoryController::class, 'show']);
Route::get('/articles/{document}/download', [ArticleController::class, 'download']);

// protected Routes
Route::middleware(['auth:sanctum',])->group(function () {

    // User management routes
    Route::delete('/users/{id}', [UsersController::class, 'destroy']);
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', [AuthController::class, 'user']);
    Route::get('/users', [UsersController::class, 'index'])
        ->name('users.index');

    
    Route::middleware(IsAdmin::class)->group(function () {
        Route::post('/articles', [ArticleController::class, 'store']);
        Route::put('/articles/{document}', [ArticleController::class, 'update']);
        Route::put('/users/{id}', [UsersController::class, 'update']);
        Route::get('/users/{id}', [UsersController::class, 'show']);
        Route::delete('/articles/{document}', [ArticleController::class, 'destroy']);
    });

    // Misc routes
    // Route::get('/background-images', [BackgroundImageController::class, 'index']);
    // Route::get('/admin/background-images', [BackgroundImageController::class, 'adminIndex']);
    // Route::post('/admin/background-images', [BackgroundImageController::class, 'store']);
    // Route::patch('/admin/background-images/{backgroundImage}', [BackgroundImageController::class, 'update']);
    // Route::delete('/admin/background-images/{backgroundImage}', [BackgroundImageController::class, 'destroy']);
    // Route::post('/admin/background-images/update-order', [BackgroundImageController::class, 'updateOrder']);
});

// Email OTP Verification
Route::post('/send-otp', [AuthController::class, 'sendOtp']);
Route::post('/verify-otp', [AuthController::class, 'verifyOtp']);
Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
    $request->fulfill();
    return response()->json(['message' => 'Email verified successfully!']);
})->middleware(['auth:sanctum', 'signed'])->name('verification.verify');

