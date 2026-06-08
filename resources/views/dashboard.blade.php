@extends('layouts.app')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <!-- Tarjetas de progreso -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
            <!-- Servicio Social -->
            <div class="bg-white overflow-hidden shadow-sm rounded-lg">
                <div class="p-6">
                    <div class="flex justify-between items-start">
                        <div>
                            <h2 class="text-xl font-bold text-gray-800">Servicio Social</h2>
                            <p class="text-sm text-gray-500 mt-1">Estado actual de tu trámite</p>
                        </div>
                        <a href="{{ route('servicio-social.index') }}" class="text-blue-600 text-sm hover:underline">Ver detalles →</a>
                    </div>
                    <div class="mt-4">
                        <div class="flex justify-between text-sm text-gray-600 mb-1">
                            <span>Progreso</span>
                            <span>{{ $progresoSS ?? 0 }}%</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2">
                            <div class="bg-blue-600 h-2 rounded-full" style="width: {{ $progresoSS ?? 0 }}%"></div>
                        </div>
                        <p class="text-xs text-gray-500 mt-2">
                            Estatus: <span class="font-semibold">{{ $estatusSS ?? 'No solicitado' }}</span>
                        </p>
                    </div>
                </div>
            </div>

            <!-- Prácticas Profesionales -->
            <div class="bg-white overflow-hidden shadow-sm rounded-lg">
                <div class="p-6">
                    <div class="flex justify-between items-start">
                        <div>
                            <h2 class="text-xl font-bold text-gray-800">Prácticas Profesionales</h2>
                            <p class="text-sm text-gray-500 mt-1">Estado actual de tu trámite</p>
                        </div>
                        <a href="{{ route('practicas.index') }}" class="text-blue-600 text-sm hover:underline">Ver detalles →</a>
                    </div>
                    <div class="mt-4">
                        <div class="flex justify-between text-sm text-gray-600 mb-1">
                            <span>Progreso</span>
                            <span>{{ $progresoPP ?? 0 }}%</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2">
                            <div class="bg-blue-600 h-2 rounded-full" style="width: {{ $progresoPP ?? 0 }}%"></div>
                        </div>
                        <p class="text-xs text-gray-500 mt-2">
                            Estatus: <span class="font-semibold">{{ $estatusPP ?? 'No solicitado' }}</span>
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Anuncios públicos -->
        <div class="bg-white overflow-hidden shadow-sm rounded-lg">
            <div class="p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center gap-2">
                    <span class="iconify w-5 h-5 text-blue-600" data-icon="mdi:bullhorn"></span>
                    Anuncios importantes
                </h3>

                @forelse($anuncios as $anuncio)
                    <div class="border-b border-gray-100 last:border-0 py-3">
                        <div class="flex justify-between items-start">
                            <div>
                                <p class="text-gray-800">{{ $anuncio->contenido }}</p>
                                <p class="text-xs text-gray-400 mt-1">
                                    Publicado por {{ $anuncio->admin->name }} • {{ $anuncio->created_at->diffForHumans() }}
                                </p>
                            </div>
                        </div>
                    </div>
                @empty
                    <p class="text-gray-500 text-sm">No hay anuncios disponibles.</p>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection