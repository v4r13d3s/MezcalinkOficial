<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('categorias_misiones', function (Blueprint $table) {
            $table->id();
            
            // Información básica
            $table->string('nombre', 100);
            $table->text('descripcion')->nullable();
            $table->string('slug')->unique();
            
            // Organización
            $table->integer('orden')->default(0); // Para ordenar las categorías
            $table->boolean('activa')->default(true);
            $table->boolean('visible')->default(true);
            
            // Configuración adicional
            $table->json('metadatos')->nullable(); // Datos extras como configuraciones especiales
            
            $table->timestamps();
            
            // Índices
            $table->index('activa');
            $table->index('visible');
            $table->index('orden');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('categorias_misiones');
    }
};
