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
        Schema::create('palenques', function (Blueprint $table) {
            $table->id();
            $table->foreignId('region_id')->nullable()->constrained('regions');
            $table->foreignId('maestro_id')->nullable()->constrained('maestros');

            $table->string('nombre');
            $table->string('slug')->unique();
            $table->text('descripcion');
            $table->text('historia')->nullable();
            $table->string('telefono')->nullable();
            $table->string('correo')->unique()->nullable();
            $table->string('direccion')->nullable();
            $table->string('redes_sociales')->nullable();
            $table->date('fecha_fundacion')->nullable();
            $table->string('capacidad_produccion')->nullable();
            $table->boolean('activo')->default(true);

            $table->timestamps();
            $table->index('activo');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('palenques');
    }
};
