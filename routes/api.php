<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\CreateThoughtController;
use App\Http\Controllers\Api\ListingThoughtController;
use App\Http\Controllers\Api\DeleteThoughtController;

Route::prefix('thoughts')->group(function () {
    Route::post('/', [CreateThoughtController::class, 'store']);
    Route::get('/', [ListingThoughtController::class, 'index']);
    Route::delete('/{thought}', [DeleteThoughtController::class, 'destroy']);
});
