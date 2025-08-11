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
        Schema::create('insignias', function (Blueprint $table) {
            $table->id();
            // Relaciones opcionales
            $table->foreignId('mision_id')->nullable()->constrained('misiones')->onDelete('set null');
            $table->foreignId('categorias_insignias_id')->nullable()->constrained('categorias_insignias')->onDelete('set null');

            // Campos principales
            $table->string('nombre', 150);
            $table->text('descripcion')->nullable();
            $table->string('slug')->unique();
            // Campos de configuración
            $table->enum('tipo', ['bronce', 'plata', 'oro', 'especial', 'evento'])->default('bronce');
            $table->enum('rareza', ['comun', 'raro', 'epico', 'legendario'])->default('comun');
            $table->integer('puntos')->default(0);
            $table->integer('orden')->default(0);
            $table->integer('progreso_maximo')->default(1); // Para barras de progreso

            // Campos de estado
            $table->boolean('activa')->default(true);
            $table->boolean('visible')->default(true);
            $table->datetime('fecha_inicio')->nullable();
            $table->datetime('fecha_fin')->nullable();

            // Campos adicionales;
            $table->json('requisitos')->nullable(); // Condiciones específicas
            $table->json('metadatos')->nullable(); // Datos adicionales

            $table->timestamps();

            // Índices
            $table->index('activa');
            $table->index('visible');
            $table->index('tipo');
            $table->index('orden');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('insignias');
    }
};
