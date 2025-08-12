<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Mezcal extends Model
{
    use HasFactory;

    protected $table = 'mezcals';

    protected $fillable = [
        'region_id',
        'marca_id',
        'tipo_elaboracion_id',
        'categoria_mezcal_id',
        'tipo_maduracion_id',
        'maestro_id',
        'palenque_id',
        'nombre',
        'slug',
        'precio_regular',
        'descripcion',
        'contenido_alcohol',
        'tamanio_bote',
        'proveedor',
        'notas_cata',
        'premios',
        'activo'
    ];

    protected $casts = [
        'activo' => 'boolean',
        'premios' => 'json',
    ];

    public function region()
    {
        return $this->belongsTo(Region::class);
    }

    public function marca()
    {
        return $this->belongsTo(Marca::class);
    }

    public function tipo_maduracion()
    {
        return $this->belongsTo(TipoMaduracion::class);
    }

    public function categoria_mezcal()
    {
        return $this->belongsTo(CategoriaMezcal::class);
    }

    public function tipo_elaboracion()
    {
        return $this->belongsTo(TipoElaboracion::class);
    }

    public function maestro()
    {
        return $this->belongsTo(Maestro::class);
    }

    public function palenque()
    {
        return $this->belongsTo(Palenque::class);
    }

    public function agaves()
    {
        return $this->belongsToMany(Agave::class, 'agaves_mezcals');
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
