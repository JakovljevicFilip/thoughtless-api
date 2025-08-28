<?php
declare(strict_types=1);

use App\Http\Controllers\Api\Auth\RegisterUserController;
use App\Http\Controllers\Api\Thoughts\CreateThoughtController;
use App\Http\Controllers\Api\Thoughts\DeleteThoughtController;
use App\Http\Controllers\Api\Thoughts\ListingThoughtController;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Route;

Route::prefix('user')->group(function () {
    Route::post('/register', [RegisterUserController::class, 'store']);
});

Route::prefix('email')->middleware('auth:sanctum')->group(function () {
    // In case someone tries to log in without having verified their email.
    Route::get('/verify', fn () =>
    response()->json(['message' => 'Please verify your email before continuing.'])
    )->name('verification.notice');

    Route::get('/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
        $request->fulfill();
        return response()->json(['message' => 'Email verified successfully.']);
    })->middleware('signed')->name('verification.verify');

    Route::post('/verification-notification', function (Request $request) {
        $request->user()->sendEmailVerificationNotification();
        return response()->json(['message' => 'Verification link resent.']);
    })->middleware('throttle:6,1')->name('verification.send');
});

Route::prefix('thoughts')->group(function () {
    Route::post('/', [CreateThoughtController::class, 'store']);
    Route::get('/', [ListingThoughtController::class, 'index']);
    Route::delete('/{thought}', [DeleteThoughtController::class, 'destroy']);
});
