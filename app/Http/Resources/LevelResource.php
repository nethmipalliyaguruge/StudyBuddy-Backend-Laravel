<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class LevelResource extends JsonResource
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
            'name' => $this->name,
            'school_id' => $this->school_id,
            'school' => new SchoolResource($this->whenLoaded('school')),
            'modules' => ModuleResource::collection($this->whenLoaded('modules')),
            'modules_count' => $this->when($this->modules_count !== null, $this->modules_count),
            'created_at' => $this->created_at?->toISOString(),
        ];
    }
}
