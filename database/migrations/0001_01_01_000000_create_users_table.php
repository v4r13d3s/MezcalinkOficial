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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->rememberToken();
            $table->foreignId('current_team_id')->nullable();
            $table->string('profile_photo_path', 2048)->nullable();

            $table->integer('nivel')->default(1);
            $table->integer('experiencia_total')->default(0);
            $table->integer('experiencia_nivel_actual')->default(0);

            $table->integer('puntos_totales')->default(0);
            $table->integer('monedas')->default(0); // Para sistema de recompensas/tienda
            $table->integer('nivel_anterior')->default(1); // Para detectar cuando sube de nivel
            $table->integer('misiones_completadas')->default(0);
            $table->integer('total_insignias')->default(0);
            $table->integer('racha_dias')->default(0);
            $table->date('ultima_actividad')->nullable();
            $table->timestamp('fecha_inicio_gamificacion')->nullable();
            $table->boolean('tutorial_completado')->default(false);

            $table->timestamps();
        });

        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('sessions');
    }
};
