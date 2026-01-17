<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Note extends Model implements HasMedia
{
    use InteractsWithMedia;
    protected $fillable = [
        'user_id',
        'module_id',
        'title',
        'description',
        'price',
        'status',
    ];

    protected $casts = [
        'price' => 'decimal:2',
    ];

    public function module(): BelongsTo
    {
        return $this->belongsTo(Module::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function isApproved(): bool
    {
        return $this->status === 'approved';
    }


    public function purchases()
    {
        return $this->hasMany(Purchase::class);
    }

    public function hasPurchases()
    {
        return $this->purchases()->exists();
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('note_file')->singleFile();
        $this->addMediaCollection('previews');
    }

}

