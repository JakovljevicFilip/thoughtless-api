<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;

final class ThoughtCollection extends ResourceCollection
{
    public function toArray($request): array
    {
        return [
            'data' => ThoughtResource::collection($this->collection),
        ];
    }

    public function with($request): array
    {
        return [
            'meta' => [
                'count' => $this->collection->count(),
            ],
        ];
    }
}
