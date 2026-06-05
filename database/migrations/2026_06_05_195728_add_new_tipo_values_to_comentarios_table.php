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
        Schema::table('comentarios', function (Blueprint $table) {
            $table->enum('tipo', [
                'admin',
                'estudiante',
                'admin_primer_informe',
                'admin_segundo_informe',
                'estudiante_primer_informe',
                'estudiante_segundo_informe'
            ])->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('comentarios', function (Blueprint $table) {
            $table->enum('tipo', [
                'admin',
                'estudiante',
                'admin_primer_informe',
                'admin_segundo_informe'
            ])->change();
        });
    }
};
