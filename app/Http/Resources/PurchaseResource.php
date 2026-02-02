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
            'user_id' => $this->user_id,
            'material_id' => $this->note_id,
            'price' => (float) $this->price,
            'amount' => (float) $this->price,
            'price_formatted' => 'LKR ' . number_format($this->price, 2),
            'transaction_id' => $this->stripe_payment_intent_id,
            'status' => $this->status,
            'payment_method' => $this->payment_method,
            'paid_at' => $this->paid_at?->toISOString(),

            // Related note/material (both keys for compatibility)
            'note' => new MaterialResource($this->whenLoaded('note')),
            'material' => new MaterialResource($this->whenLoaded('note')),

            // Timestamps
            'created_at' => $this->created_at->toISOString(),
            'updated_at' => $this->updated_at->toISOString(),
        ];
    }
}
