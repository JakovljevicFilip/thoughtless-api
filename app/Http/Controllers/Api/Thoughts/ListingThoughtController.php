<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Thoughts;

use App\Http\Controllers\Controller;
use App\Http\Resources\ThoughtResource;
use App\Models\Thought;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

final class ListingThoughtController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $thoughts = Thought::query()
            ->orderByDesc('created_at')
            ->get();

        return ThoughtResource::collection($thoughts);
    }
}
