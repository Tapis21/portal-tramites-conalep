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
            $table->string('cargo_persona_carta')->nullable()->after('nombre_persona_carta');
            $table->string('nombre_jefe_inmediato')->nullable()->after('cargo_persona_carta');
            $table->string('cargo_jefe_inmediato')->nullable()->after('nombre_jefe_inmediato');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('servicio_social', function (Blueprint $table) {
            $table->dropColumn(['cargo_persona_carta', 'nombre_jefe_inmediato', 'cargo_jefe_inmediato']);
        });
    }
};
