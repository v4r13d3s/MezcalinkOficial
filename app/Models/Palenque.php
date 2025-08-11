<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Palenque extends Model
{
    use HasFactory;

    protected $table = 'palenques';

    protected $fillable = [
        'region_id',
        'maestro_id',
        'nombre',
        'slug',
        'descripcion',
        'historia',
        'telefono',
        'correo',
        'direccion',
        'redes_sociales',
        'fecha_fundacion',
        'capacidad_produccion',
        'activo'
    ];

    protected $casts = [
        'fecha_fundacion' => 'date',
        'activo' => 'boolean',
    ];

    public function maestro()
    {
        return $this->belongsTo(Maestro::class);
    }

    public function region()
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
