@extends('layouts.app')

@section('content')
<div class="py-12">
    <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-xl rounded-lg p-6">
            <h2 class="text-2xl font-bold mb-5">Subir Primer Informe de Actividades - Prácticas Profesionales</h2>

            @if ($errors->any())
                <div class="bg-red-100 border-l-4 border-red-500 text-red-800 p-3 rounded mb-4">
                    <ul class="list-disc list-inside">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- 👇 NUEVO: Mostrar información del trámite -->
            <div class="mb-4 p-4 bg-blue-50 border border-blue-200 rounded-md">
                <div class="grid grid-cols-2 gap-2 text-sm">
                    <div>
                        <span class="font-semibold text-gray-600">Estudiante:</span>
                        <span class="text-gray-800">{{ Auth::user()->name }} {{ Auth::user()->apellidos }}</span>
                    </div>
                    <div>
                        <span class="font-semibold text-gray-600">Matrícula:</span>
                        <span class="text-gray-800">{{ Auth::user()->matricula }}</span>
                    </div>
                    <div>
                        <span class="font-semibold text-gray-600">Carrera:</span>
                        <span class="text-gray-800">{{ Auth::user()->carrera }}</span>
                    </div>
                    <div>
                        <span class="font-semibold text-gray-600">Fecha límite:</span>
                        <span class="text-red-600 font-medium">{{ optional($practica->fecha_limite_parcial)->format('d/m/Y') }}</span>
                    </div>
                </div>
            </div>

            <p class="mb-4 text-sm text-gray-600">
                <span class="font-semibold">Nota:</span> Este es el primer informe de actividades correspondiente a las primeras 180 horas de prácticas profesionales.
            </p>

            <form method="POST" action="{{ route('practicas.guardar-reporte-parcial', $practica->id) }}" enctype="multipart/form-data">
                @csrf

                <div class="mb-4">
                    <label class="block text-gray-700 font-medium mb-2">
                        Archivo PDF (Primer Informe) <span class="text-red-500">*</span>
                    </label>
                    <input type="file" 
                           name="reporte_pdf" 
                           accept=".pdf" 
                           required 
                           class="w-full p-2 border rounded-md focus:border-blue-500 focus:ring focus:ring-blue-200 transition">
                    <p class="text-xs text-gray-500 mt-1">Máximo 5MB. Solo archivos PDF.</p>
                </div>

                <div class="mb-4">
                    <label class="block text-gray-700 font-medium mb-2">Comentario (opcional)</label>
                    <textarea name="comentario" 
                              rows="3" 
                              class="w-full p-2 border rounded-md focus:border-blue-500 focus:ring focus:ring-blue-200 transition" 
                              placeholder="Agrega un comentario para el administrador sobre tu informe..."></textarea>
                    <p class="text-xs text-gray-500 mt-1">Máximo 500 caracteres.</p>
                </div>

                <div class="flex items-center gap-3 mt-6">
                    <button type="submit" 
                            class="bg-blue-600 hover:bg-blue-700 text-white font-semibold px-6 py-2 rounded-md transition">
                        <span class="iconify inline mr-1" data-icon="mdi:cloud-upload"></span>
                        Subir Informe
                    </button>
                    <a href="{{ route('practicas.index') }}" 
                       class="bg-gray-200 hover:bg-gray-300 text-gray-700 font-semibold px-6 py-2 rounded-md transition">
                        Cancelar
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection