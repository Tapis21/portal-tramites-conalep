@extends('layouts.app')

@section('content')
<div class="max-w-2xl mx-auto py-10">
    <h1 class="text-2xl font-bold mb-5">Subir Reporte Final</h1>

    @if ($errors->any())
        <div class="bg-red-100 text-red-800 p-3 rounded mb-4">
            <ul class="list-disc list-inside">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="bg-white p-6 rounded shadow">
        <p class="mb-2"><strong>Horas completadas:</strong> {{ $servicioSocial->horas_completadas }} / 480</p>
        <p class="mb-4 text-sm text-gray-600">Requisito mínimo: 480 horas</p>

        <form method="POST" action="{{ route('servicio-social.guardar-reporte-final', $servicioSocial->id) }}" enctype="multipart/form-data">
            @csrf

            <div class="mb-4">
                <label class="block text-gray-700 mb-2">Reporte final (PDF)</label>
                <input type="file" name="reporte_pdf" accept=".pdf" required 
                       class="w-full p-2 border rounded">
                <p class="text-xs text-gray-500 mt-1">Máximo 5MB. Solo archivos PDF.</p>
            </div>

            <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded">
                Subir reporte final
            </button>
            <a href="{{ route('servicio-social.index') }}" 
               class="ml-2 text-gray-600 hover:text-gray-800">
                Cancelar
            </a>
        </form>
    </div>
</div>
@endsection
