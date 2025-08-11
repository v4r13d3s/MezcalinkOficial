<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('images', function (Blueprint $table) {
            $table->id();
            
            // Campos polimórficos
            $table->morphs('imageable'); // Esto creará imageable_id e imageable_type
            
            // Campos de la imagen
            $table->string('path'); // ruta de la imagen
            $table->string('name')->nullable();
            $table->string('alt')->nullable(); // Texto alternativo para SEO
            $table->text('description')->nullable();
            $table->enum('type', ['logo', 'banner', 'gallery', 'profile', 'thumbnail'])->default('gallery');
            $table->integer('order')->default(0);
            $table->boolean('is_featured')->default(false);
            $table->boolean('is_active')->default(true);
            
            // Metadatos de la imagen
            $table->string('mime_type')->nullable();
            $table->integer('size')->nullable(); // en bytes
            $table->integer('width')->nullable();
            $table->integer('height')->nullable();
            
            $table->timestamps();
            
            // Índices
            $table->index('order');
            $table->index('type');
            $table->index('is_active');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('images');
    }
};
