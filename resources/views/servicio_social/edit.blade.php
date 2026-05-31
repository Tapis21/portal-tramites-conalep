@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto py-10">
    <h1 class="text-2xl font-bold mb-5">Actualizar horas completadas</h1>

    <form method="POST" action="{{ route('servicio-social.update', $servicioSocial->id) }}">
        @csrf
        @method('PUT')

        <div class="mb-4">
            <label class="block text-gray-700">Horas completadas</label>
            <input type="number" name="horas_completadas" 
                   value="{{ old('horas_completadas', $servicioSocial->horas_completadas) }}"
                   class="w-full p-2 border rounded" min="0" max="480" required>
        </div>

        <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded">
            Guardar
        </button>
        <a href="{{ route('servicio-social.index') }}" class="ml-2 text-gray-600 hover:text-gray-800">
            Cancelar
        </a>
    </form>
</div>
@endsection
