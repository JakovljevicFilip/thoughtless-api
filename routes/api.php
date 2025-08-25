<?php

use App\Http\Controllers\Api\Thoughts\CreateThoughtController;
use App\Http\Controllers\Api\Thoughts\DeleteThoughtController;
use App\Http\Controllers\Api\Thoughts\ListingThoughtController;
use Illuminate\Support\Facades\Route;

Route::prefix('thoughts')->group(function () {
    Route::post('/', [CreateThoughtController::class, 'store']);
    Route::get('/', [ListingThoughtController::class, 'index']);
    Route::delete('/{thought}', [DeleteThoughtController::class, 'destroy']);
});
