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
            $table->foreignId('grado_academico_jefe_id')
                  ->nullable()
                  ->after('cargo_jefe_inmediato')
                  ->constrained('grados_academicos')
                  ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('servicio_social', function (Blueprint $table) {
            $table->dropForeign(['grado_academico_jefe_id']);
            $table->dropColumn('grado_academico_jefe_id');
        });
    }
};
