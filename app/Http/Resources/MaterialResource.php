<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MaterialResource extends JsonResource
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
            'description' => $this->description,
            'price' => (float) $this->price,
            'price_formatted' => 'LKR ' . number_format($this->price, 2),
            'status' => $this->status,
            'module_id' => $this->module_id,
            'user_id' => $this->user_id,

            // Relationships
            'module' => new ModuleResource($this->whenLoaded('module')),
            'user' => $this->whenLoaded('user', function () {
                return [
                    'id' => $this->user->id,
                    'name' => $this->user->name,
                    'profile_photo_url' => $this->user->profile_photo_url,
                ];
            }),

            // Media files
            'note_file' => $this->whenLoaded('media', function () {
                $media = $this->getFirstMedia('note_file');
                return $media ? [
                    'id' => $media->id,
                    'file_name' => $media->file_name,
                    'mime_type' => $media->mime_type,
                    'size' => $media->size,
                    'size_formatted' => $this->formatFileSize($media->size),
                ] : null;
            }),
            'previews' => $this->whenLoaded('media', function () {
                return $this->getMedia('previews')->map(function ($media) {
                    return [
                        'id' => $media->id,
                        'url' => $media->getUrl(),
                    ];
                });
            }),

            // Hierarchy for filtering display
            'hierarchy' => $this->whenLoaded('module', function () {
                $module = $this->module;
                $level = $module->level ?? null;
                $school = $level->school ?? null;

                return [
                    'school' => $school ? [
                        'id' => $school->id,
                        'name' => $school->name,
                    ] : null,
                    'level' => $level ? [
                        'id' => $level->id,
                        'name' => $level->name,
                    ] : null,
                    'module' => [
                        'id' => $module->id,
                        'title' => $module->title,
                    ],
                ];
            }),

            'created_at' => $this->created_at->toISOString(),
            'updated_at' => $this->updated_at->toISOString(),
        ];
    }

    /**
     * Format file size to human readable format.
     */
    private function formatFileSize(int $bytes): string
    {
        $units = ['B', 'KB', 'MB', 'GB'];
        $i = 0;
        while ($bytes >= 1024 && $i < count($units) - 1) {
            $bytes /= 1024;
            $i++;
        }
        return round($bytes, 2) . ' ' . $units[$i];
    }
}
