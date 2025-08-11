<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Marca extends Model
{

    use HasFactory;

    protected $fillable = [
        'region_id',
        'nombre',
        'slug',
        'certificado_dom',
        'descripcion',
        'historia',
        'eslogan',
        'anio_fundacion',
        'telefono',
        'correo',
        'redes_sociales',
        'sitio_web',
        'activo',
    ];

    protected $casts = [
        'activo' => 'boolean',
    ];

    public function regions()
    {
        return $this->belongsTo(Region::class);
    }

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
