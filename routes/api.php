<?php
declare(strict_types=1);

use App\Http\Controllers\Api\Auth\RegisterUserController;
use App\Http\Controllers\Api\Auth\ResendVerificationController;
use App\Http\Controllers\Api\Auth\VerifyEmailController;
use App\Http\Controllers\Api\Thoughts\CreateThoughtController;
use App\Http\Controllers\Api\Thoughts\DeleteThoughtController;
use App\Http\Controllers\Api\Thoughts\ListingThoughtController;
use Illuminate\Support\Facades\Route;

Route::prefix('user')->group(function () {
    Route::post('/register', [RegisterUserController::class, 'store']);
});

Route::prefix('email')->group(function () {
    // Notice shown if trying to use app without verifying - logging in without being verified.
    Route::get('/verify', fn () =>
    response()->json(['message' => 'Please verify your email before continuing.'])
    )->name('verification.notice');

    Route::post('/verify', VerifyEmailController::class)->name('verification.verify');
    Route::post('/resend', ResendVerificationController::class)
        ->middleware('throttle:1,10') // 1 request per 10 minutes per IP
        ->name('verification.resend');
});

Route::prefix('thoughts')->group(function () {
    Route::post('/', [CreateThoughtController::class, 'store']);
    Route::get('/', [ListingThoughtController::class, 'index']);
    Route::delete('/{thought}', [DeleteThoughtController::class, 'destroy']);
});
