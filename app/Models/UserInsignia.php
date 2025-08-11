<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserInsignia extends Model
{
    use HasFactory;

    protected $table = 'users_insignias';

    protected $fillable = [
        'user_id',
        'insignia_id',
        'progreso',
        'porcentaje',
        'completada',
        'fecha_obtenida',
        'fecha_inicio_progreso',
        'datos_progreso',
        'notas',
    ];

    protected $casts = [
        'fecha_obtenida' => 'datetime',
        'fecha_inicio_progreso' => 'datetime',
        'datos_progreso' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function insignia()
    {
        return $this->belongsTo(Insignia::class);
    }
}