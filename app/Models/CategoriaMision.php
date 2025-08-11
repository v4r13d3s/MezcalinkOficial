<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CategoriaMision extends Model
{
    use HasFactory;

    protected $table = 'categorias_misiones';

    protected $fillable = [
        'nombre',
        'descripcion',
        'slug',
        'orden',
        'activa',
        'visible',
        'metadatos'
    ];

    protected $casts = [
        'activa' => 'boolean',
        'visible' => 'boolean',
        'metadatos' => 'array', // Para manejar metadatos como un array
    ];

    public function misiones()
    {
        return $this->hasMany(Mision::class);
    }


    // Relación para todas las imágenes
    public function images()
    {
        return $this->morphMany(Image::class, 'imageable');
    }

    // Para obtener solo el logo
    public function logo()
    {
        return $this->morphOne(Image::class, 'imageable')
            ->where('type', 'logo');
    }

    // Para obtener la galería
    public function gallery()
    {
        return $this->morphMany(Image::class, 'imageable')
            ->where('type', 'gallery')
            ->orderBy('order');
    }
}
