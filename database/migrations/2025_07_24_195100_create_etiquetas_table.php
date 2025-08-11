<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('etiquetas', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 45);
            $table->string('slug', 45)->unique();
            $table->string('color', 7)->nullable(); // Para el color de la etiqueta en hex (#FFFFFF)
            $table->boolean('activa')->default(true);
            $table->timestamps();

            // Índices
            $table->index('activa');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('etiquetas');
    }
};
