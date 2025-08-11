<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Mision extends Model
{
    use HasFactory;

    protected $table = 'misiones';

    protected $fillable = [
        'categorias_misiones_id',
        'titulo',
        'descripcion',
        'slug',
        'tipo',
        'dificultad',
        'tiempo_estimado',
        'objetivo_descripcion',
        'objetivo_cantidad',
        'puntos_experiencia',
        'puntos_moneda',
        'fecha_inicio',
        'fecha_fin',
        'tiempo_limite_horas',
        'activa',
        'visible',
        'orden',
        'nivel_minimo',
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

    public function users()
    {
        return $this->belongsToMany(User::class, 'usuario_misiones')
                    ->withPivot('estado', 'progreso_actual', 'porcentaje', 'fecha_inicio', 'fecha_completada', 'fecha_expiracion', 'intentos', 'datos_progreso', 'notas')
                    ->withTimestamps();
    }

    // Relación directa con tabla pivot
    public function progresoUsuarios()
    {
        return $this->hasMany(UserMision::class);
    }

    public function categorias_misiones(): BelongsTo
    {
        return $this->belongsTo(CategoriaMision::class);
    }

    public function insignias()
    {
        return $this->hasMany(Insignia::class);
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
