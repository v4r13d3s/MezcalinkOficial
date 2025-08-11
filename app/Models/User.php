<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Laravel\Jetstream\HasProfilePhoto;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens;

    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory;
    use HasProfilePhoto;
    use Notifiable;
    use TwoFactorAuthenticatable;

    protected $table = 'users';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'profile_photo_path',
        'nivel',
        'experiencia_total',
        'experiencia_nivel_actual',
        'puntos_totales',
        'monedas',
        'nivel_anterior',
        'misiones_completadas',
        'total_insignias',
        'racha_dias',
        'ultima_actividad',
        'fecha_inicio_gamificacion',
        'tutorial_completado'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_recovery_codes',
        'two_factor_secret',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array<int, string>
     */
    protected $appends = [
        'profile_photo_url',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'ultima_actividad' => 'date',
            'fecha_inicio_gamificacion' => 'datetime',
            'tutorial_completado' => 'boolean',
            'nivel' => 'integer',
            'experiencia_total' => 'integer',
            'experiencia_nivel_actual' => 'integer',
            'puntos_totales' => 'integer',
            'monedas' => 'integer',
            'nivel_anterior' => 'integer',
            'misiones_completadas' => 'integer',
            'total_insignias' => 'integer',
            'racha_dias' => 'integer'
        ];
    }

    public function misiones()
    {
        return $this->belongsToMany(Mision::class, 'users_misiones')
                    ->withPivot('estado', 'progreso_actual', 'porcentaje', 'fecha_inicio', 'fecha_completada', 'fecha_expiracion', 'intentos', 'datos_progreso', 'notas')
                    ->withTimestamps();
    }

    // Relación Many-to-Many con Insignias (a través de usuario_insignias)
    public function insignias()
    {
        return $this->belongsToMany(Insignia::class, 'users_insignias')
                    ->withPivot('progreso', 'porcentaje', 'completada', 'fecha_obtenida', 'fecha_inicio_progreso', 'datos_progreso', 'notas')
                    ->withTimestamps();
    }

    // Relaciones directas con las tablas pivot (hasMany)
    public function progresoMisiones()
    {
        return $this->hasMany(UserMision::class);
    }

    public function progresoInsignias()
    {
        return $this->hasMany(UserInsignia::class);
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
