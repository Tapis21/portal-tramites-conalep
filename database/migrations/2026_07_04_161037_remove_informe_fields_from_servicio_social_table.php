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
            // ✅ Solo eliminar las columnas que realmente existen
            // Verificado con db:table: existen estas columnas
            $table->dropColumn('reporte_parcial_subido');
            $table->dropColumn('reporte_final_subido');
            $table->dropColumn('archivo_parcial');
            $table->dropColumn('archivo_final');
            $table->dropColumn('estatus_parcial');
            $table->dropColumn('estatus_final');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('servicio_social', function (Blueprint $table) {
            // Revertir los cambios en caso de rollback
            $table->boolean('reporte_parcial_subido')->default(false);
            $table->boolean('reporte_final_subido')->default(false);
            $table->string('archivo_parcial')->nullable();
            $table->string('archivo_final')->nullable();
            $table->enum('estatus_parcial', ['pendiente', 'validado', 'validado_ventanilla', 'rechazado'])->default('pendiente');
            $table->enum('estatus_final', ['pendiente', 'validado', 'validado_ventanilla', 'rechazado'])->default('pendiente');
        });
    }
};
