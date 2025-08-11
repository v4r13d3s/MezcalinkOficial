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
        Schema::create('misiones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('categorias_misiones_id')->nullable()->constrained('categorias_misiones');
            

            $table->string('titulo');
            $table->string('descripcion')->nullable();
            $table->string('slug')->unique();
            $table->enum('tipo', ['diaria', 'semanal', 'unica', 'evento', 'cadena']);
            
            //Campos de dificultad y recompensa
            $table->enum('dificultad', ['facil', 'medio', 'dificil']);
            $table->integer('tiempo_estimado')->nullable();

            // Objetivo
            $table->string('objetivo_descripcion'); // Ej: "Completa 5 tareas"
            $table->integer('objetivo_cantidad')->default(1); // Cuántas veces

            // Recompensa
            $table->integer('puntos_experiencia')->default(0);
            $table->integer('puntos_moneda')->default(0);

            // Tiempo (opcional)
            $table->datetime('fecha_inicio')->nullable();
            $table->datetime('fecha_fin')->nullable();
            $table->integer('tiempo_limite_horas')->nullable(); // Horas para completar

            // Estado y orden
            $table->boolean('activa')->default(true);
            $table->boolean('visible')->default(true);
            $table->integer('orden')->default(0);
            $table->integer('nivel_minimo')->default(1);
            
            // Configuración adicional (para crecer después)
            $table->json('requisitos')->nullable(); // Condiciones para desbloquear
            $table->json('metadatos')->nullable(); // Datos extras
            $table->timestamps();

            // Índices
            $table->index('activa');
            $table->index('visible');
            $table->index('tipo');
            $table->index('dificultad');
            $table->index('orden');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('misiones');
    }
};
