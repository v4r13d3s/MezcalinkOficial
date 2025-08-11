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
        Schema::create('agaves', function (Blueprint $table) {
            $table->id();
            $table->foreignId('region_id')->nullable()->constrained('regions');
            $table->string('nombre');
            $table->string('slug')->unique();
            $table->string('nombre_cientifico');
            $table->text('usos')->nullable();
            $table->decimal('altura')->nullable();
            $table->decimal('diametro')->nullable();
            $table->text('descripcion')->nullable();
            $table->string('tiempo_maduracion')->nullable();
            $table->boolean('activo')->default(true);
            $table->integer('orden')->default(0);

            $table->timestamps();

            $table->index('activo');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('agaves');
    }
};
