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
        Schema::table('documentos_estatus', function (Blueprint $table) {
            DB::statement("ALTER TABLE documentos MODIFY COLUMN estatus ENUM('pendiente', 'validado', 'rechazado', 'validado_ventanilla') NOT NULL DEFAULT 'pendiente'");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('documentos_estatus', function (Blueprint $table) {
            DB::statement("ALTER TABLE documentos MODIFY COLUMN estatus ENUM('pendiente', 'validado', 'rechazado') NOT NULL DEFAULT 'pendiente'");
        });
    }
};
