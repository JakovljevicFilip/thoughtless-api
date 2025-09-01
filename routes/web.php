<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\Auth\Web\LoginWebController;
use App\Http\Controllers\Api\Auth\Web\LogoutWebController;

Route::get('/', function () {
    return view('welcome');
});

Route::prefix('api/auth/web')->group(function () {
    Route::post('/login',  [LoginWebController::class, 'store']);
    Route::post('/logout', [LogoutWebController::class, 'store'])->middleware('auth:web');
});
