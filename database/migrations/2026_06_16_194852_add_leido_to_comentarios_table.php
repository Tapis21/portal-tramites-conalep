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
            // Agregar campo leido (booleano, por defecto false)
            $table->boolean('leido')->default(false)->after('user_id');
            
            // Agregar campo leido_at (timestamp cuando se marcó como leído)
            $table->timestamp('leido_at')->nullable()->after('leido');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('comentarios', function (Blueprint $table) {
            $table->dropColumn(['leido', 'leido_at']);
        });
    }
};
