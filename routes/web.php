<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

Route::get('/{any}', function () {
    return view('login');
})->where('any', '.*');

Route::get('/login', function () {
    return response()->json(['message' => 'Please log in via the API'], 401);
})->name('login');
