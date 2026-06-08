@extends('layouts.app')

@section('content')
<div class="max-w-2xl mx-auto mt-40 px-4">
    <div class="bg-yellow-50 border-l-4 border-yellow-400 p-6 rounded-lg shadow-md">
        <h2 class="text-xl font-bold text-yellow-800 mb-2">Aún no has solicitado Prácticas Profesionales</h2>
        <p class="text-gray-700 mb-4">
            Completa el formulario de solicitud para comenzar.
        </p>
        <a href="{{ route('solicitud-practicas.create') }}" class="inline-flex items-center gap-2 bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">
            Ir al formulario
        </a>
    </div>
</div>
@endsection