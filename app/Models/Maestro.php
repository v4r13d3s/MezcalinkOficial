<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Maestro extends Model
{
    use HasFactory;

    protected $fillable = [
        'region_id',
        'nombre',
        'slug',
        'fecha_nacimiento',
        'genero',
        'nacionalidad',
        'telefono',
        'correo',
        'foto',
        'anios_experiencia',
        'biografia',
    ];

    public function palenque()
    {
        return $this->hasMany(Palenque::class);
    }

    public function mezcals()
    {
        return $this->hasMany(Mezcal::class);
    }

    public function region()
    {
        return $this->belongsTo(Region::class);
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
