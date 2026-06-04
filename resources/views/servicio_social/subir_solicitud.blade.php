@extends('layouts.app')

@section('content')
<div class="py-12">
    <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-xl rounded-lg p-6">
            <h2 class="text-2xl font-bold mb-5">Subir Solicitud</h2>

            @if ($errors->any())
                <div class="bg-red-100 text-red-800 p-3 rounded mb-4">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <p class="mb-4 text-sm text-gray-600">La solicitud es un documento requerido para iniciar el trámite de Servicio Social.</p>

            <form method="POST" action="{{ route('servicio-social.guardar-solicitud', $servicioSocial->id) }}" enctype="multipart/form-data">
                @csrf

                <div class="mb-4">
                    <label class="block text-gray-700 mb-2">Archivo PDF (Solicitud)</label>
                    <input type="file" name="archivo_pdf" accept=".pdf" required class="w-full p-2 border rounded">
                    <p class="text-xs text-gray-500 mt-1">Máximo 5MB. Solo archivos PDF.</p>
                </div>

                <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded">Subir solicitud</button>
                <a href="{{ route('servicio-social.index') }}" class="ml-2 text-gray-600">Cancelar</a>
            </form>
        </div>
    </div>
</div>
@endsection