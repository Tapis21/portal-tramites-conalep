<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('matricula')->unique()->after('id');
            $table->string('apellidos')->after('name'); // name es el nombre
            $table->string('carrera')->after('apellidos');
            $table->enum('role', ['admin', 'estudiante'])->default('estudiante')->after('carrera');
            $table->integer('semestre')->nullable()->after('role');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['matricula', 'apellidos', 'carrera', 'role', 'semestre']);
        });
    }
};