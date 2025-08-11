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
        Schema::create('mezcals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('region_id')->nullable()->constrained('regions');
            $table->foreignId('marca_id')->nullable()->constrained('marcas');
            $table->foreignId('tipo_elaboracion_id')->nullable()->constrained('tipo_elaboracion');
            $table->foreignId('categoria_mezcal_id')->nullable()->constrained('categoria_mezcal');
            $table->foreignId('tipo_maduracion_id')->nullable()->constrained('tipo_maduracion');
            $table->foreignId('maestro_id')->nullable()->constrained('maestros');
            $table->foreignId('palenque_id')->nullable()->constrained('palenques');

            $table->string('nombre');
            $table->string('slug')->unique();
            $table->decimal('precio_regular')->nullable();
            $table->string('descripcion')->nullable();
            $table->decimal('contenido_alcohol')->nullable();
            $table->string('tamanio_bote')->nullable();
            $table->string('proveedor')->nullable();
            $table->text('notas_cata')->nullable();
            $table->json('premios')->nullable();
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
        Schema::dropIfExists('mezcals');
    }
};
