<?php
declare(strict_types=1);
namespace App\Http\Controllers\Api\Thoughts;

use App\Http\Controllers\Controller;
use App\Http\Requests\Thoughts\DeleteThoughtRequest;
use App\Models\Thought;
use Illuminate\Http\Response;

class DeleteThoughtController extends Controller
{
    public function destroy(DeleteThoughtRequest $request, Thought $thought): Response
    {
        $thought->delete();
        return response()->noContent();
    }
}
