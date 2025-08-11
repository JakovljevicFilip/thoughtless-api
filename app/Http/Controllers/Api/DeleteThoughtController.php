<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Thought;
use Illuminate\Http\Response;

class DeleteThoughtController extends Controller
{
    public function destroy(Thought $thought): Response
    {
        $thought->delete();

        return response()->noContent();
    }
}
