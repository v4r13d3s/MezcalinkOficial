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
        Schema::create('users_misiones', function (Blueprint $table) {
            $table->id();
            
            // Relaciones
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('mision_id')->constrained('misiones')->onDelete('cascade');
            
            // Estado y progreso
            $table->enum('estado', ['pendiente', 'en_progreso', 'completada', 'fallida', 'expirada'])
                  ->default('pendiente');
            $table->integer('progreso_actual')->default(0); // Cuánto ha avanzado
            $table->decimal('porcentaje', 5, 2)->default(0.00); // Porcentaje completado
            
            // Fechas importantes
            $table->datetime('fecha_inicio')->nullable(); // Cuándo empezó la misión
            $table->datetime('fecha_completada')->nullable(); // Cuándo la terminó
            $table->datetime('fecha_expiracion')->nullable(); // Cuándo expira
            
            // Datos adicionales
            $table->integer('intentos')->default(0); // Cuántas veces la ha intentado
            $table->json('datos_progreso')->nullable(); // Para tracking detallado
            $table->text('notas')->nullable(); // Notas del progreso
            
            $table->timestamps();
            
            // Índices y constraints
            $table->unique(['user_id', 'mision_id']); // Un usuario solo puede tener una instancia de cada misión
            $table->index('estado');
            $table->index('fecha_completada');
            $table->index('progreso_actual');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users_misiones');
    }
};
