<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('estudiante_periodo', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('periodo_id')->constrained()->onDelete('cascade');
            $table->boolean('activo')->default(true);
            $table->date('fecha_asignacion');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('estudiante_periodo');
    }
};