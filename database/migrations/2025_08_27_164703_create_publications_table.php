<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // tabla de publicaciones en la base de datos
        Schema::create('publications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->text('contenido');
            $table->json('redes');
            $table->enum('tipo_publicacion', ['inmediata', 'programada'])->default('inmediata');
            $table->datetime('fecha_hora')->nullable();
            $table->enum('estado', ['pendiente', 'en_proceso', 'completada', 'error'])->default('pendiente');
            $table->json('resultados')->nullable();
            $table->timestamps();
            
            $table->index(['user_id', 'estado']);
            $table->index(['tipo_publicacion', 'fecha_hora']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('publications');
    }
};