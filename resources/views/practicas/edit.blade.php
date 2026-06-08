@extends('layouts.app')

@section('content')
<div class="max-w-2xl mx-auto py-10">
    <div class="bg-white p-6 rounded shadow">
        <h1 class="text-2xl font-bold mb-5">Actualizar horas de Prácticas Profesionales</h1>

        <form method="POST" action="{{ route('practicas.update', $practica->id) }}">
            @csrf
            @method('PUT')

            <div class="mb-4">
                <label class="block text-gray-700 font-semibold mb-2">Horas completadas</label>
                <input type="number" name="horas_completadas" value="{{ old('horas_completadas', $practica->horas_completadas ?? 0) }}"
                       class="w-full p-2 border rounded" min="0" max="360" required>
                <p class="text-xs text-gray-500 mt-1">Total requeridas: 360 horas</p>
            </div>

            <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Guardar</button>
            <a href="{{ route('practicas.index') }}" class="ml-2 text-gray-600">Cancelar</a>
        </form>
    </div>
</div>
@endsection