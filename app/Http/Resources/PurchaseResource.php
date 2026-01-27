<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PurchaseResource extends JsonResource
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
            'price' => (float) $this->price,
            'price_formatted' => 'LKR ' . number_format($this->price, 2),
            'status' => $this->status,
            'payment_method' => $this->payment_method,
            'paid_at' => $this->paid_at?->toISOString(),

            // Related note/material
            'note' => new MaterialResource($this->whenLoaded('note')),

            // Timestamps
            'created_at' => $this->created_at->toISOString(),
        ];
    }
}
