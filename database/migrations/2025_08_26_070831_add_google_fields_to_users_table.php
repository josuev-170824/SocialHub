<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{   
    // Migracion para agregar los campos google_id y avatar a la tabla users
    public function up(): void
    {
        // Agregar los campos google_id y avatar a la tabla users
        Schema::table('users', function (Blueprint $table) {
            $table->string('google_id')->nullable()->unique()->after('id');
            $table->string('avatar')->nullable()->after('email');
        });
    }
    // Migracion para eliminar los campos google_id y avatar de la tabla users
    public function down(): void
    {
        // Eliminar los campos google_id y avatar de la tabla users 
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['google_id','avatar']);
        });
    }
};
