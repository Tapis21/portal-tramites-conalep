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
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'estatus_practicas')) {
                $table->enum('estatus_practicas', [
                    'no_solicitado', 
                    'pendiente', 
                    'en_progreso', 
                    'liberado'
                ])->default('no_solicitado')->after('estatus_servicio_social');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'estatus_practicas')) {
                $table->dropColumn('estatus_practicas');
            }
        });
    }
};
