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
        Schema::create('categorias_insignias', function (Blueprint $table) {
            $table->id();

            $table->string('nombre');
            $table->text('descripcion')->nullable();
            $table->string('slug')->unique();
            $table->integer('orden')->default(0);
            $table->boolean('activa')->default(true);
            $table->boolean('visible')->default(true);
            
            $table->timestamps();
            
            // Índices
            $table->index('activa');
            $table->index('orden');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('categorias_insignias');
    }
};
