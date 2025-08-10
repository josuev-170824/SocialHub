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
        // Agregar campos para 2FA
        Schema::table('users', function (Blueprint $table) {
            $table->string('google2fa_secret')->nullable()->after('password'); // Campo para el secreto de Google Authenticator
            $table->boolean('google2fa_enabled')->default(false)->after('google2fa_secret'); // Campo para verificar si el 2FA está activo
            $table->timestamp('google2fa_enabled_at')->nullable()->after('google2fa_enabled'); // Campo para la fecha de activación del 2FA
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Eliminar campos para 2FA
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['google2fa_secret', 'google2fa_enabled', 'google2fa_enabled_at']);
        });
    }
};