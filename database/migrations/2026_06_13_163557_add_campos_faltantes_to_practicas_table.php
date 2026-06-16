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
            $table->string('cargo_persona_carta')->nullable()->after('nombre_persona_carta');
            $table->string('nombre_jefe_inmediato')->nullable()->after('cargo_persona_carta');
            $table->string('cargo_jefe_inmediato')->nullable()->after('nombre_jefe_inmediato');
            $table->foreignId('grado_academico_jefe_id')->nullable()->after('cargo_jefe_inmediato')->constrained('grados_academicos')->onDelete('set null');
            $table->foreignId('horario_id')->nullable()->after('fecha_limite_final')->constrained('horarios')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('practicas', function (Blueprint $table) {
            $table->dropForeign(['grado_academico_jefe_id']);
            $table->dropForeign(['horario_id']);
            $table->dropColumn([
                'cargo_persona_carta',
                'nombre_jefe_inmediato',
                'cargo_jefe_inmediato',
                'grado_academico_jefe_id',
                'horario_id'
            ]);
        });
    }
};
