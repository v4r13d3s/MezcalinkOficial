<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TipoMaduracion extends Model
{
    use HasFactory;

    protected $table = 'tipo_maduracion';

    protected $fillable = [
        'nombre',
        'slug',
        'descripcion',
        'activo',
        'orden'
    ];

    protected $cast = [
        'orden' => 'integer',
        'activo' => 'boolean',
    ];

    public function mezcals()
    {
        return $this->hasMany(Mezcal::class);
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
