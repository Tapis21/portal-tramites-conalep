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
        Schema::table('practicas', function (Blueprint $table) {
            // Agregar columnas si no existen
            if (!Schema::hasColumn('practicas', 'empresa_id')) {
                $table->foreignId('empresa_id')->nullable()->constrained('empresas')->nullOnDelete();
            }
            if (!Schema::hasColumn('practicas', 'grado_academico_id')) {
                $table->foreignId('grado_academico_id')->nullable()->constrained('grados_academicos')->nullOnDelete();
            }
            if (!Schema::hasColumn('practicas', 'nombre_persona_carta')) {
                $table->string('nombre_persona_carta')->nullable();
            }
            if (!Schema::hasColumn('practicas', 'area_asignada')) {
                $table->string('area_asignada')->nullable();
            }
            if (!Schema::hasColumn('practicas', 'apoyo_estudiante')) {
                $table->string('apoyo_estudiante')->nullable();
            }
            if (!Schema::hasColumn('practicas', 'fecha_inicio')) {
                $table->date('fecha_inicio')->nullable();
            }
            if (!Schema::hasColumn('practicas', 'fecha_limite_parcial')) {
                $table->date('fecha_limite_parcial')->nullable();
            }
            if (!Schema::hasColumn('practicas', 'fecha_limite_final')) {
                $table->date('fecha_limite_final')->nullable();
            }
            if (!Schema::hasColumn('practicas', 'estatus')) {
                $table->enum('estatus', ['no_solicitado', 'pendiente', 'en_progreso', 'liberado'])->default('no_solicitado');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('practicas', function (Blueprint $table) {
            $table->dropForeign(['empresa_id']);
            $table->dropForeign(['grado_academico_id']);
            $table->dropColumn([
                'empresa_id',
                'grado_academico_id',
                'nombre_persona_carta',
                'area_asignada',
                'apoyo_estudiante',
                'fecha_inicio',
                'fecha_limite_parcial',
                'fecha_limite_final',
                'estatus'
            ]);
        });
    }
};
