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
        Schema::create('users_insignias', function (Blueprint $table) {
            $table->id();
            
            // Relaciones
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('insignia_id')->constrained('insignias')->onDelete('cascade');
            
            // Progreso y estado
            $table->integer('progreso')->default(0); // Progreso actual (0 hasta progreso_maximo)
            $table->decimal('porcentaje', 5, 2)->default(0.00); // Porcentaje de progreso (0.00 a 100.00)
            $table->boolean('completada')->default(false);
            $table->datetime('fecha_obtenida')->nullable(); // Cuando completó la insignia
            $table->datetime('fecha_inicio_progreso')->nullable(); // Cuando empezó el progreso
            
            // Datos adicionales
            $table->json('datos_progreso')->nullable(); // Para tracking detallado
            $table->text('notas')->nullable(); // Notas adicionales
            
            $table->timestamps();
            
            // Índices y constraints
            $table->unique(['user_id', 'insignia_id']); // Un usuario no puede tener la misma insignia duplicada
            $table->index('completada');
            $table->index('fecha_obtenida');
            $table->index('progreso');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users_insignias');
    }
};
