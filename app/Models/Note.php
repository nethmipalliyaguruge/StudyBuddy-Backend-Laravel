<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
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
        'is_approved',
    ];

    protected $casts = [
        'is_approved' => 'boolean',
        'price' => 'decimal:2',
    ];

    public function module()
    {
        return $this->belongsTo(Module::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('note_file')->singleFile();
        $this->addMediaCollection('previews');
    }

}

