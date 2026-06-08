@extends('layouts.app')

@section('content')
<div style="margin-top: 25vh;">
    <div class="max-w-2xl mx-auto px-4">
        <!-- Botón Atrás (solo visible en móvil) -->
        <div class="sm:hidden mb-4">
            <a href="{{ route('dashboard') }}" class="inline-flex items-center gap-2 text-blue-600 hover:text-blue-800 transition">
                <span class="iconify w-5 h-5" data-icon="mdi:arrow-left"></span>
                Volver al inicio
            </a>
        </div>

        <div class="bg-yellow-50 border-l-4 border-yellow-400 p-6 rounded-lg shadow-md">
            <div class="flex items-center gap-3 mb-4">
                <span class="iconify w-8 h-8 text-yellow-600" data-icon="mdi:alert-circle"></span>
                <h2 class="text-xl font-bold text-yellow-800">Requisito no cumplido</h2>
            </div>
            <p class="text-gray-700 mb-4">
                Para acceder al módulo de <strong>Prácticas Profesionales</strong>, primero debes completar y liberar tu <strong>Servicio Social</strong>.
            </p>
            <a href="{{ route('servicio-social.index') }}" class="inline-flex items-center gap-2 bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition">
                <span class="iconify w-5 h-5" data-icon="mdi:hand-heart"></span>
                Ir a mi Servicio Social
            </a>
        </div>
    </div>
</div>
@endsection