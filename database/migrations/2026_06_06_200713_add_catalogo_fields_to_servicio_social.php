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
        Schema::table('servicio_social', function (Blueprint $table) {
            // Relación con empresas
            $table->foreignId('empresa_id')->nullable()->constrained('empresas')->nullOnDelete();
            // Relación con grados académicos
            $table->foreignId('grado_academico_id')->nullable()->constrained('grados_academicos')->nullOnDelete();
            // Campos que escribe el estudiante
            $table->string('nombre_persona_carta')->nullable(); // a quién va dirigida la carta
            $table->string('area_asignada')->nullable();
            $table->string('apoyo_estudiante')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('servicio_social', function (Blueprint $table) {
            $table->dropForeign(['empresa_id']);
            $table->dropForeign(['grado_academico_id']);
            $table->dropColumn(['empresa_id', 'grado_academico_id', 'nombre_persona_carta', 'area_asignada', 'apoyo_estudiante']);
        });
    }
};
