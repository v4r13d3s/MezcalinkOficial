<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('categorias_blog', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->string('slug')->unique();
            $table->text('descripcion')->nullable();
            $table->boolean('activa')->default(true);
            $table->integer('orden')->default(0);
            $table->timestamps();

            // Índices
            $table->index('activa');
            $table->index('orden');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('categorias_blog');
    }
};
