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
            // 1. Eliminar columnas obsoletas
            $table->dropColumn([
                'horas_requeridas',
                'horas_completadas',
                'comentario_admin_parcial',
                'comentario_admin_final'
            ]);

            // 2. Agregar nuevas columnas de fechas
            $table->date('fecha_inicio')->nullable()->after('user_id');
            $table->date('fecha_limite_primer_informe')->nullable()->after('fecha_inicio');
            $table->date('fecha_limite_segundo_informe')->nullable()->after('fecha_limite_primer_informe');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('servicio_social', function (Blueprint $table) {
            // Revertir: restaurar columnas eliminadas
            $table->integer('horas_requeridas')->default(480);
            $table->integer('horas_completadas')->default(0);
            $table->text('comentario_admin_parcial')->nullable();
            $table->text('comentario_admin_final')->nullable();

            // Eliminar columnas de fechas agregadas
            $table->dropColumn([
                'fecha_inicio',
                'fecha_limite_primer_informe',
                'fecha_limite_segundo_informe'
            ]);
        });
    }
};
