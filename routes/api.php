<?php
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Api\ThoughtController;

Route::post('/thoughts', [ThoughtController::class, 'store']);
