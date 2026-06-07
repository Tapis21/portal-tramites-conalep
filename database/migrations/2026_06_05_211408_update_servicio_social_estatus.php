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
            $table->enum('estatus', ['pendiente', 'en_progreso', 'liberado'])->default('pendiente')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('servicio_social', function (Blueprint $table) {
            $table->enum('estatus', ['pendiente', 'en_progreso', 'pendiente_revision', 'liberado'])->default('pendiente')->change();
        });
    }
};
