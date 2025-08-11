<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Agave extends Model
{
    use HasFactory;

    protected $fillable = [
        'region_id',
        'nombre',
        'slug',
        'nombre_cientifico',
        'usos',
        'altura',
        'diametro',
        'descripcion',
        'tiempo_maduracion',
        'activo',
        'orden',
    ];

    protected $casts = [
        'orden' => 'integer',
        'activo' => 'boolean',
    ];

    public function region()
    {
        return $this->belongsTo(Region::class);
    }


    public function mezcals()
    {
        return $this->belongsToMany(Mezcal::class, 'agaves_mezcals');
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
