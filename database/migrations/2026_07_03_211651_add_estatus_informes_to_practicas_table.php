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
            // Eliminar los campos booleanos antiguos
            $table->dropColumn([
                'reporte_parcial_validado',
                'reporte_parcial_rechazado',
                'reporte_final_validado',
                'reporte_final_rechazado'
            ]);

            // Agregar los nuevos campos de estatus
            $table->enum('estatus_parcial', ['pendiente', 'validado', 'validado_ventanilla', 'rechazado'])
                  ->default('pendiente')
                  ->after('archivo_parcial');
            $table->enum('estatus_final', ['pendiente', 'validado', 'validado_ventanilla', 'rechazado'])
                  ->default('pendiente')
                  ->after('archivo_final');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('practicas', function (Blueprint $table) {
            $table->dropColumn(['estatus_parcial', 'estatus_final']);
            $table->boolean('reporte_parcial_validado')->default(false);
            $table->boolean('reporte_parcial_rechazado')->default(false);
            $table->boolean('reporte_final_validado')->default(false);
            $table->boolean('reporte_final_rechazado')->default(false);
        });
    }
};
