<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('publicaciones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('categoria_blog_id')->constrained('categorias_blog')->onDelete('cascade');
            
            $table->string('titulo');
            $table->string('slug')->unique();
            $table->text('extracto')->nullable();
            $table->longText('contenido');
            
            // Metadatos SEO
            $table->string('meta_titulo')->nullable();
            $table->text('meta_descripcion')->nullable();
            $table->string('meta_keywords')->nullable();
            
            // Estado y programación
            $table->enum('estado', ['borrador', 'publicado', 'programado'])->default('borrador');
            $table->timestamp('fecha_publicacion')->nullable();
            $table->boolean('destacada')->default(false);
            $table->boolean('permite_comentarios')->default(true);
            
            // Estadísticas
            $table->integer('vistas')->default(0);
            $table->integer('tiempo_lectura')->nullable(); // en minutos
            
            $table->timestamps();
            
            // Índices
            $table->index('estado');
            $table->index('destacada');
            $table->index('fecha_publicacion');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('publicaciones');
    }
};
