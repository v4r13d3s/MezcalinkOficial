<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Publicacion extends Model
{
    protected $table = 'publicaciones';

    protected $fillable = [
        'user_id',
        'categoria_blog_id',
        'titulo',
        'slug',
        'extracto',
        'contenido',
        'meta_titulo',
        'meta_descripcion',
        'meta_keywords',
        'estado',
        'fecha_publicacion',
        'destacada',
        'permite_comentarios',
        'vistas',
        'tiempo_lectura'
    ];

    protected $casts = [
        'destacada' => 'boolean',
        'permite_comentarios' => 'boolean',
        'fecha_publicacion' => 'datetime',
        'vistas' => 'integer',
        'tiempo_lectura' => 'integer'
    ];

    /**
     * Obtiene el usuario autor de la publicación
     */
    public function autor()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Obtiene la categoría de la publicación
     */
    public function categoria()
    {
        return $this->belongsTo(CategoriaBlog::class, 'categoria_blog_id');
    }

    /**
     * Obtiene las etiquetas de la publicación
     */
    public function etiquetas()
    {
        return $this->belongsToMany(Etiqueta::class, 'publicacion_etiqueta')
                    ->withTimestamps();
    }

    /**
     * Obtiene todas las imágenes asociadas a la publicación
     */
    public function imagenes()
    {
        return $this->morphMany(Image::class, 'imageable');
    }

    /**
     * Obtiene la imagen principal
     */
    public function imagen_principal()
    {
        return $this->morphOne(Image::class, 'imageable')
                    ->where('type', 'thumbnail')
                    ->where('is_featured', true);
    }

    /**
     * Obtiene la galería de imágenes
     */
    public function galeria()
    {
        return $this->morphMany(Image::class, 'imageable')
                    ->where('type', 'gallery')
                    ->orderBy('order');
    }

    /**
     * Scope para publicaciones publicadas
     */
    public function scopePublicadas($query)
    {
        return $query->where('estado', 'publicado')
                    ->where('fecha_publicacion', '<=', now());
    }

    /**
     * Scope para publicaciones destacadas
     */
    public function scopeDestacadas($query)
    {
        return $query->where('destacada', true);
    }
}
