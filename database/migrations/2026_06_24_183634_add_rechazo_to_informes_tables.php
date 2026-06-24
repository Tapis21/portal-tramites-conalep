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
        // Para Servicio Social
        Schema::table('servicio_social', function (Blueprint $table) {
            $table->boolean('reporte_parcial_rechazado')->default(false)->after('reporte_parcial_validado');
            $table->boolean('reporte_final_rechazado')->default(false)->after('reporte_final_validado');
        });

        // Para Prácticas Profesionales
        Schema::table('practicas', function (Blueprint $table) {
            $table->boolean('reporte_parcial_rechazado')->default(false)->after('reporte_parcial_validado');
            $table->boolean('reporte_final_rechazado')->default(false)->after('reporte_final_validado');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('servicio_social', function (Blueprint $table) {
            $table->dropColumn(['reporte_parcial_rechazado', 'reporte_final_rechazado']);
        });

        Schema::table('practicas', function (Blueprint $table) {
            $table->dropColumn(['reporte_parcial_rechazado', 'reporte_final_rechazado']);
        });
    }
};
