<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserMision extends Model
{
    use HasFactory;

    protected $table = 'users_misiones';

    protected $fillable = [
        'user_id',
        'mision_id',
        'estado',
        'progreso_actual',
        'porcentaje',
        'fecha_inicio',
        'fecha_completada',
        'fecha_expiracion',
        'intentos',
        'datos_progreso',
        'notas'
    ];

    protected $casts = [
        'fecha_inicio' => 'datetime',
        'fecha_completada' => 'datetime',
        'fecha_expiracion' => 'datetime',
        'datos_progreso' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function mision()
    {
        return $this->belongsTo(Mision::class);
    }
}
