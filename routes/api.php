<?php
declare(strict_types=1);

use App\Http\Controllers\Api\Auth\AccountRemovalController;
use App\Http\Controllers\Api\Auth\CancellationController;
use App\Http\Controllers\Api\Auth\MeController;
use App\Http\Controllers\Api\Auth\Mobile\LoginMobileController;
use App\Http\Controllers\Api\Auth\Mobile\LogoutMobileController;
use App\Http\Controllers\Api\Auth\PasswordResetController;
use App\Http\Controllers\Api\Auth\PasswordResetLinkController;
use App\Http\Controllers\Api\Auth\PasswordResetVerifyController;
use App\Http\Controllers\Api\Auth\RegisterUserController;
use App\Http\Controllers\Api\Auth\ResendVerificationController;
use App\Http\Controllers\Api\Auth\VerifyEmailController;
use App\Http\Controllers\Api\Auth\Web\LoginWebController;
use App\Http\Controllers\Api\Auth\Web\LogoutWebController;
use App\Http\Controllers\Api\Thoughts\CreateThoughtController;
use App\Http\Controllers\Api\Thoughts\DeleteThoughtController;
use App\Http\Controllers\Api\Thoughts\ListingThoughtController;
use App\Http\Controllers\Api\Thoughts\Store\StoreController;
use Illuminate\Foundation\Http\Middleware\ValidateCsrfToken;
use Illuminate\Support\Facades\Route;
use Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful;

Route::prefix('user')->group(function () {

    Route::post('/register', [RegisterUserController::class, 'store']);
    Route::post('/forgot-password', [PasswordResetLinkController::class, 'store']);
    Route::post('/forgot-password/verify', [PasswordResetVerifyController::class, 'store']);
    Route::post('/forgot-password/reset', [PasswordResetController::class, 'store']);

    Route::middleware('auth:sanctum')->group(function () {
        Route::post('remove', [AccountRemovalController::class, 'store']);
    });
    Route::post('cancel-removal', [CancellationController::class, 'store']);
});

Route::prefix('email')->group(function () {
    Route::get('/verify', fn () => response()->json(['message' => 'Please verify your email before continuing.']))
        ->name('verification.notice');

    Route::post('/verify', VerifyEmailController::class)->name('verification.verify');
    Route::post('/resend', ResendVerificationController::class)
        ->middleware('throttle:3,10')
        ->name('verification.resend');
});

// TODO: How will I verify protected routes in the future? Will mobile token work for other routes if it does not have csrf?
Route::prefix('auth/mobile')
    ->withoutMiddleware(EnsureFrontendRequestsAreStateful::class)
    ->withoutMiddleware(ValidateCsrfToken::class)
    ->group(function () {
        Route::post('/login',  [LoginMobileController::class, 'store']);
        Route::post('/logout', [LogoutMobileController::class, 'store'])
            ->middleware('auth:sanctum');
    });

Route::prefix('auth/web')
    ->middleware('spa')
    ->group(function () {
        Route::post('/login', [LoginWebController::class, 'store']);
        Route::post('/logout', [LogoutWebController::class, 'store'])->middleware('auth:web');
    });

Route::middleware('auth:sanctum')->get('/me', MeController::class);

Route::middleware('auth:sanctum')->group(function () {
//    TODO: Move to 'thought' prefix
    Route::prefix('thoughts')->group(function () {
        Route::post('/', [CreateThoughtController::class, 'store']);
        Route::get('/', [ListingThoughtController::class, 'index']);
        Route::delete('/{thought}', [DeleteThoughtController::class, 'destroy']);
    });

    Route::prefix('thought')->group(function () {
        Route::post('/store', [StoreController::class, 'store']);
    });
});

Route::get('/sanctum/csrf-cookie', [\Laravel\Sanctum\Http\Controllers\CsrfCookieController::class, 'show'])
    ->middleware('web');

Route::get('/debug-test', function () {
    return response()->json(['ok' => true, 'time' => now()]);
});

