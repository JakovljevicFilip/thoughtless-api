<?php
declare(strict_types=1);

namespace App\Http\Controllers\Api\Auth\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

class LogoutWebController extends Controller
{
    public function store(): Response
    {
        Auth::guard('web')->logout();
        request()->session()->invalidate();
        request()->session()->regenerateToken();

        return response()->noContent();
    }
}
