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
            if (!Schema::hasColumn('servicio_social', 'grado_academico_id')) {
                $table->foreignId('grado_academico_id')->nullable()->constrained('grados_academicos')->nullOnDelete();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('servicio_social', function (Blueprint $table) {
            $table->dropForeign(['grado_academico_id']);
            $table->dropColumn('grado_academico_id');
        });
    }
};
