<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Image extends Model
{
    protected $fillable = [
        'path',
        'name',
        'alt',
        'description',
        'type',
        'order',
        'is_featured',
        'is_active',
        'mime_type',
        'size',
        'width',
        'height'
    ];

    public function imageable()
    {
        return $this->morphTo();
    }
}