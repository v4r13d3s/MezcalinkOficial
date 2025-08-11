<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CategoriaBlog extends Model
{
    protected $table = 'categorias_blog';

    protected $fillable = [
        'nombre',
        'slug',
        'descripcion',
        'activa',
        'orden'
    ];

    protected $casts = [
        'activa' => 'boolean',
    ];

    /**
     * Obtiene las publicaciones de esta categoría
     */
    public function publicaciones()
    {
        return $this->hasMany(Publicacion::class, 'categoria_blog_id');
    }

    /**
     * Obtiene las imágenes asociadas a esta categoría
     */
    public function imagenes()
    {
        return $this->morphMany(Image::class, 'imageable');
    }

    /**
     * Obtiene la imagen destacada
     */
    public function imagen()
    {
        return $this->morphOne(Image::class, 'imageable')->where('type', 'thumbnail');
    }
}
