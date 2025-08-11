<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Region extends Model
{
    use HasFactory;

    protected $fillable = [
        'nombre',
        'codigo'
    ];

    public function palenques()
    {
        return $this->hasMany(Palenque::class);
    }

    public function maestros()
    {
        return $this->hasMany(Maestro::class);
    }

    public function agaves()
    {
        return $this->hasMany(Agave::class);
    }

    public function marcas()
    {
        return $this->hasMany(Marca::class);
    }

    public function mezcals()
    {
        return $this->hasMany(Mezcal::class);
    }
}
