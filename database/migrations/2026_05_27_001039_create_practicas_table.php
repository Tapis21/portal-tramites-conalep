<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('practicas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->integer('horas_requeridas')->default(360);
            $table->integer('horas_completadas')->default(0);
            $table->boolean('reporte_parcial_subido')->default(false);
            $table->boolean('reporte_parcial_validado')->default(false);
            $table->boolean('reporte_final_subido')->default(false);
            $table->boolean('reporte_final_validado')->default(false);
            $table->string('archivo_parcial')->nullable();
            $table->string('archivo_final')->nullable();
            $table->enum('estatus', ['pendiente', 'en_progreso', 'liberado'])->default('pendiente');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('practicas');
    }
};