<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ModuleResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'level_id' => $this->level_id,
            'status' => $this->status,
            'level' => new LevelResource($this->whenLoaded('level')),
            'notes_count' => $this->when($this->notes_count !== null, $this->notes_count),
            'created_at' => $this->created_at?->toISOString(),
        ];
    }
}
