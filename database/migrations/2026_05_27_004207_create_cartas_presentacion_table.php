<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cartas_presentacion', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('empresa');
            $table->text('direccion');
            $table->string('contacto');
            $table->string('puesto');
            $table->date('fecha_inicio');
            $table->date('fecha_termino');
            $table->string('pdf_generado')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cartas_presentacion');
    }
};