<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Etiqueta extends Model
{
    protected $fillable = [
        'nombre',
        'slug',
        'color',
        'activa'
    ];

    protected $casts = [
        'activa' => 'boolean',
    ];

    /**
     * Obtiene las publicaciones que tienen esta etiqueta
     */
    public function publicaciones()
    {
        return $this->belongsToMany(Publicacion::class, 'publicacion_etiqueta')
                    ->withTimestamps();
    }

    /**
     * Obtiene la imagen asociada a esta etiqueta
     */
    public function imagen()
    {
        return $this->morphOne(Image::class, 'imageable')->where('type', 'thumbnail');
    }
}
