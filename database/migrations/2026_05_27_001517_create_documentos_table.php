<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('documentos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('tipo_documento_id')->constrained('tipos_documento')->onDelete('cascade');
            $table->string('archivo_pdf'); // Ruta del archivo
            $table->enum('estatus', ['pendiente', 'validado', 'rechazado'])->default('pendiente');
            $table->text('comentario')->nullable(); // Razón de rechazo
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('documentos');
    }
};