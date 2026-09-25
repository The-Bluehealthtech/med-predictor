<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class TeamCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'data' => $this->collection,
            'meta' => [
                'current_page' => (int) $this->currentPage(),
                'last_page' => (int) $this->lastPage(),
                'per_page' => (int) $this->perPage(),
                'total' => (int) $this->total(),
                'from' => $this->firstItem() !== null ? (int) $this->firstItem() : null,
                'to' => $this->lastItem() !== null ? (int) $this->lastItem() : null,
            ],
            'links' => [
                'first' => $this->url(1),
                'last' => $this->url($this->lastPage()),
                'prev' => $this->previousPageUrl(),
                'next' => $this->nextPageUrl(),
            ],
        ];
    }
} 