<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\CreateThoughtController;
use App\Http\Controllers\Api\ListingThoughtController;

Route::prefix('thoughts')->group(function () {
    Route::post('/', [CreateThoughtController::class, 'store']);
    Route::get('/', [ListingThoughtController::class, 'index']);
});
