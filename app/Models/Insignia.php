<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Insignia extends Model
{
    use HasFactory;

    protected $fillable = [
        'mision_id',
        'categorias_insignias_id',
        'nombre',
        'descripcion',
        'slug',
        'tipo',
        'rareza',
        'puntos',
        'orden',
        'progreso_maximo',
        'activa',
        'visible',
        'fecha_inicio',
        'fecha_fin',
        'requisitos',
        'metadatos'
    ];

    protected $casts = [
        'activa' => 'boolean',
        'visible' => 'boolean',
        'requisitos' => 'array',
        'metadatos' => 'array',
        'fecha_inicio' => 'datetime',
        'fecha_fin' => 'datetime',
    ];

    // Relación Many-to-Many con Insignias (a través de usuario_insignias)
    public function users()
    {
        return $this->belongsToMany(Insignia::class, 'users_insignias')
            ->withPivot('progreso', 'porcentaje', 'completada', 'fecha_obtenida', 'fecha_inicio_progreso', 'datos_progreso', 'notas')
            ->withTimestamps();
    }

    // Relación directa con tabla pivot
    public function progresoUsuarios()
    {
        return $this->hasMany(UserInsignia::class);
    }

    public function categorias_insignias(): BelongsTo
    {
        return $this->belongsTo(CategoriaInsignia::class);
    }

    public function misiones()
    {
        return $this->belongsTo(Mision::class);
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
