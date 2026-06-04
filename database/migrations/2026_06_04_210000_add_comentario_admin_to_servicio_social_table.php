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
            $table->text('comentario_admin_parcial')->nullable()->after('archivo_final');
            $table->text('comentario_admin_final')->nullable()->after('comentario_admin_parcial');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('servicio_social', function (Blueprint $table) {
            $table->dropColumn(['comentario_admin_parcial', 'comentario_admin_final']);
        });
    }
};
