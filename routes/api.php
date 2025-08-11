<?php
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Api\CreateThoughtController;

Route::post('/thoughts', [CreateThoughtController::class, 'store']);
