<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Module extends Model
{
    protected $fillable = [
        'level_id',
        'title',
        'status',
    ];

    protected $casts = [
        'status' => 'boolean',
        ];

    public function level()
    {
        return $this->belongsTo(Level::class);
    }

    public function notes()
    {
        return $this->hasMany(Note::class);
    }
}

