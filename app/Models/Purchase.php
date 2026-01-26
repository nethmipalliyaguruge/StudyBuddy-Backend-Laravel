<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Purchase extends Model
{
    protected $fillable = [
        'user_id',
        'note_id',
        'price',
        'commission',
        'status',
        'stripe_session_id',
        'stripe_payment_intent_id',
        'payment_method',
        'paid_at',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'commission' => 'decimal:2',
        'paid_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function note()
    {
        return $this->belongsTo(Note::class);
    }
}

