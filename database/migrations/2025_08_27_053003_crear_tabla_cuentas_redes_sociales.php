<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cuentas_redes_sociales', function (Blueprint $table) { // Tabla 'cuentas_redes_sociales'
            // Columnas de la tabla
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('plataforma'); // 'twitter', 'facebook', 'linkedin'
            $table->string('id_plataforma'); // ID del usuario en la red social
            $table->string('nombre_usuario'); // @usuario, nombre de página, etc.
            $table->text('token_acceso'); // Token de acceso OAuth
            $table->text('token_refresco')->nullable(); // Token de refresco (si aplica)
            $table->timestamp('token_expiracion')->nullable(); // Cuándo expira el token
            $table->json('datos_adicionales')->nullable(); // Datos extra de la API
            $table->boolean('activa')->default(true); // Si la cuenta está activa
            $table->timestamps();
            
            // Índices para búsquedas rápidas (optimiza las consultas)... 
            $table->index(['user_id', 'plataforma']);
            $table->unique(['user_id', 'plataforma', 'id_plataforma']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cuentas_redes_sociales');
    }
};