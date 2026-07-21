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
        // Insertar tipos de documento para informes de Servicio Social
        DB::table('tipos_documento')->insert([
            [
                'nombre' => 'Primer Informe de Actividades Trimestral',
                'tramite' => 'SS',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nombre' => 'Segundo Informe de Actividades Trimestral',
                'tramite' => 'SS',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nombre' => 'Primer Informe de Actividades',
                'tramite' => 'PP',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nombre' => 'Segundo Informe de Actividades',
                'tramite' => 'PP',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('tipos_documento')
            ->whereIn('nombre', [
                'Primer Informe de Actividades Trimestral',
                'Segundo Informe de Actividades Trimestral',
                'Primer Informe de Actividades',
                'Segundo Informe de Actividades',
            ])
            ->delete();
    }
};
