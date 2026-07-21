@extends('layouts.app')

@section('content')
<div class="max-w-6xl mx-auto px-4 sm:px-6 py-6 sm:py-10">

    <!-- ========================================== -->
    <!-- ENCABEZADO -->
    <!-- ========================================== -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-6">
        <div class="flex items-center gap-3">
            <span class="iconify w-9 h-9 sm:w-11 sm:h-11 text-green-700" data-icon="mdi:handshake"></span>
            <div>
                <h1 class="text-2xl sm:text-3xl font-bold text-gray-900">Convenios y Empresas</h1>
                <p class="text-xs text-gray-500 hidden sm:block">Empresas aliadas para Servicio Social y Prácticas Profesionales</p>
            </div>
        </div>
        <div class="mt-2 sm:mt-0">
            <a href="{{ route('dashboard') }}" class="inline-flex items-center gap-1.5 text-sm text-gray-500 hover:text-green-700 transition">
                <span class="iconify w-4 h-4" data-icon="mdi:arrow-left"></span>
                Volver al inicio
            </a>
        </div>
    </div>

    <!-- ========================================== -->
    <!-- FILTROS -->
    <!-- ========================================== -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-200/80 p-4 mb-6">
        <div class="flex flex-wrap items-center gap-3">
            <span class="text-sm font-medium text-gray-700">Filtrar por:</span>
            <div class="flex flex-wrap gap-2">
                <a href="{{ route('empresas.index', ['filtro' => 'todos']) }}" 
                   class="px-4 py-1.5 text-sm rounded-lg transition-all duration-200 {{ $filtro == 'todos' ? 'bg-green-700 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                    Todas
                </a>
                <a href="{{ route('empresas.index', ['filtro' => 'ss']) }}" 
                   class="px-4 py-1.5 text-sm rounded-lg transition-all duration-200 {{ $filtro == 'ss' ? 'bg-green-700 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                    <span class="iconify w-4 h-4 inline mr-1 align-middle" data-icon="mdi:hand-heart"></span>
                    Servicio Social
                </a>
                <a href="{{ route('empresas.index', ['filtro' => 'pp']) }}" 
                   class="px-4 py-1.5 text-sm rounded-lg transition-all duration-200 {{ $filtro == 'pp' ? 'bg-green-700 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                    <span class="iconify w-4 h-4 inline mr-1 align-middle" data-icon="mdi:briefcase"></span>
                    Prácticas
                </a>
                <a href="{{ route('empresas.index', ['filtro' => 'ambos']) }}" 
                   class="px-4 py-1.5 text-sm rounded-lg transition-all duration-200 {{ $filtro == 'ambos' ? 'bg-green-700 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                    <span class="iconify w-4 h-4 inline mr-1 align-middle" data-icon="mdi:check-circle"></span>
                    Ambos
                </a>
            </div>
            <span class="ml-auto text-xs text-gray-400">{{ $empresas->count() }} {{ $empresas->count() == 1 ? 'empresa' : 'empresas' }}</span>
        </div>
    </div>

    <!-- ========================================== -->
    <!-- LISTADO DE EMPRESAS -->
    <!-- ========================================== -->
    @if($empresas->count() > 0)
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach($empresas as $empresa)
                <div class="group bg-white rounded-2xl shadow-sm border border-gray-200/80 overflow-hidden transition-all duration-300 hover:shadow-md hover:border-green-200 hover:-translate-y-1">
                    <div class="p-5">
                        <!-- Nombre y badge de convenio -->
                        <div class="flex items-start justify-between mb-3">
                            <div class="flex items-center gap-2.5 min-w-0">
                                <div class="w-10 h-10 rounded-xl bg-green-50 flex items-center justify-center flex-shrink-0 group-hover:bg-green-100 transition-colors duration-300">
                                    <span class="iconify w-5 h-5 text-green-700" data-icon="mdi:office-building"></span>
                                </div>
                                <h3 class="font-semibold text-gray-800 text-sm sm:text-base leading-tight truncate" title="{{ $empresa->nombre }}">
                                    {{ $empresa->nombre }}
                                </h3>
                            </div>
                            @if($empresa->servicio_social && $empresa->practicas)
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 bg-emerald-100 text-emerald-700 text-[10px] font-bold rounded-full whitespace-nowrap flex-shrink-0">
                                    <span class="iconify w-3 h-3" data-icon="mdi:check-circle"></span>
                                    Ambos
                                </span>
                            @endif
                        </div>

                        <!-- Dirección -->
                        @if($empresa->direccion)
                            <div class="flex items-start gap-2 text-xs text-gray-500 mb-1.5">
                                <span class="iconify w-3.5 h-3.5 mt-0.5 text-gray-400 flex-shrink-0" data-icon="mdi:map-marker"></span>
                                <span class="line-clamp-2">{{ $empresa->direccion }}</span>
                            </div>
                        @endif

                        <!-- Teléfono -->
                        @if($empresa->telefono)
                            <div class="flex items-center gap-2 text-xs text-gray-500 mb-1.5">
                                <span class="iconify w-3.5 h-3.5 text-gray-400 flex-shrink-0" data-icon="mdi:phone"></span>
                                <span>{{ $empresa->telefono }}</span>
                            </div>
                        @endif

                        <!-- Contacto -->
                        @if($empresa->contacto)
                            <div class="flex items-center gap-2 text-xs text-gray-500 mb-3">
                                <span class="iconify w-3.5 h-3.5 text-gray-400 flex-shrink-0" data-icon="mdi:account"></span>
                                <span>{{ $empresa->contacto }}</span>
                            </div>
                        @endif

                        <!-- Badges de SS y PP -->
                        <div class="flex flex-wrap gap-1.5 mt-1">
                            @if($empresa->servicio_social)
                                <span class="inline-flex items-center gap-1 px-2.5 py-1 bg-blue-50 text-blue-700 text-[10px] font-medium rounded-full">
                                    <span class="iconify w-3 h-3" data-icon="mdi:check-circle"></span>
                                    Servicio Social
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1 px-2.5 py-1 bg-gray-100 text-gray-400 text-[10px] font-medium rounded-full">
                                    <span class="iconify w-3 h-3" data-icon="mdi:close-circle"></span>
                                    Sin SS
                                </span>
                            @endif

                            @if($empresa->practicas)
                                <span class="inline-flex items-center gap-1 px-2.5 py-1 bg-amber-50 text-amber-700 text-[10px] font-medium rounded-full">
                                    <span class="iconify w-3 h-3" data-icon="mdi:check-circle"></span>
                                    Prácticas
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1 px-2.5 py-1 bg-gray-100 text-gray-400 text-[10px] font-medium rounded-full">
                                    <span class="iconify w-3 h-3" data-icon="mdi:close-circle"></span>
                                    Sin PP
                                </span>
                            @endif

                            @if($empresa->programa_dual)
                                <span class="inline-flex items-center gap-1 px-2.5 py-1 bg-purple-50 text-purple-700 text-[10px] font-medium rounded-full">
                                    <span class="iconify w-3 h-3" data-icon="mdi:school"></span>
                                    Dual
                                </span>
                            @endif
                        </div>

                        <!-- Fecha de convenio -->
                        @if($empresa->fecha_termino_convenio)
                            <div class="mt-3 pt-2 border-t border-gray-100 text-[10px] text-gray-400 flex items-center gap-1">
                                <span class="iconify w-3 h-3" data-icon="mdi:calendar-clock"></span>
                                Convenio vigente hasta: {{ $empresa->fecha_termino_convenio->format('d/m/Y') }}
                            </div>
                        @else
                            <div class="mt-3 pt-2 border-t border-gray-100 text-[10px] text-gray-400 flex items-center gap-1">
                                <span class="iconify w-3 h-3" data-icon="mdi:infinity"></span>
                                Convenio vigente indefinido
                            </div>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <!-- ========================================== -->
        <!-- ESTADO VACÍO -->
        <!-- ========================================== -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-200/80 p-10 text-center">
            <div class="max-w-sm mx-auto">
                <div class="w-24 h-24 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-4">
                    <span class="iconify w-12 h-12 text-gray-300" data-icon="mdi:office-building-outline"></span>
                </div>
                <h3 class="text-lg font-semibold text-gray-800">No hay empresas disponibles</h3>
                <p class="text-sm text-gray-500 mt-1">No se encontraron empresas con los filtros seleccionados.</p>
                <a href="{{ route('empresas.index', ['filtro' => 'todos']) }}" class="inline-flex items-center gap-2 mt-4 px-5 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-medium rounded-lg transition shadow-sm hover:shadow">
                    <span class="iconify w-4 h-4" data-icon="mdi:refresh"></span>
                    Ver todas
                </a>
            </div>
        </div>
    @endif

</div>
@endsection