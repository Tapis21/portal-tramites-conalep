@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 py-6 sm:py-10">

    <!-- ========================================== -->
    <!-- ENCABEZADO DEL DASHBOARD -->
    <!-- ========================================== -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-8">
        <div>
            <h1 class="text-2xl sm:text-3xl font-bold text-gray-900 flex items-center gap-3">
                <span class="iconify w-8 h-8 sm:w-10 sm:h-10 text-green-800" data-icon="mdi:view-dashboard"></span>
                Panel de Control
            </h1>
            <p class="text-sm text-gray-600 mt-1 flex items-center gap-1.5">
                <span class="iconify w-4 h-4" data-icon="mdi:account"></span>
                Bienvenido, <strong>{{ Auth::user()->name }} {{ Auth::user()->apellidos }}</strong>
                <span class="text-gray-400 mx-1">•</span>
                <span class="inline-flex items-center gap-1 text-green-700">
                    <span class="iconify w-3.5 h-3.5" data-icon="mdi:check-circle"></span>
                    Estudiante activo
                </span>
            </p>
        </div>
        <div class="mt-3 sm:mt-0">
            <span class="inline-flex items-center gap-2 px-4 py-2 bg-gray-100 border border-gray-200 rounded-lg text-sm text-gray-700">
                <span class="iconify w-4 h-4" data-icon="mdi:calendar-clock"></span>
                {{ \Carbon\Carbon::now()->locale('es')->isoFormat('dddd, D [de] MMMM [del] YYYY') }}
            </span>
        </div>
    </div>

    <!-- ========================================== -->
    <!-- TARJETAS DE PROGRESO CON CÍRCULOS -->
    <!-- ========================================== -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
        <!-- Servicio Social -->
        <div class="bg-[#f8f8f8] rounded-xl shadow-md border border-gray-200/80 overflow-hidden transition hover:shadow-lg hover:border-green-200">
            <div class="p-6">
                <div class="flex justify-between items-start mb-4">
                    <div>
                        <div class="flex items-center gap-2">
                            <div class="w-9 h-9 bg-green-50 rounded-lg flex items-center justify-center">
                                <span class="iconify w-5 h-5 text-green-700" data-icon="mdi:briefcase-account"></span>
                            </div>
                            <div>
                                <h2 class="text-lg font-bold text-gray-900">Servicio Social</h2>
                                <p class="text-xs text-gray-500">Estado actual de tu trámite</p>
                            </div>
                        </div>
                    </div>
                    <a href="{{ route('servicio-social.index') }}" class="inline-flex items-center gap-0.5 text-green-700 hover:text-green-800 text-sm font-medium transition group">
                        Ver detalles
                        <span class="iconify w-4 h-4 group-hover:translate-x-0.5 transition" data-icon="mdi:arrow-right"></span>
                    </a>
                </div>

                <!-- Círculo de progreso + Info -->
                <div class="flex flex-col sm:flex-row items-center gap-6">
                    <div class="relative flex-shrink-0">
                        @php
                            $progreso = min($progresoSS ?? 0, 100);
                            $circunferencia = 2 * pi() * 45;
                            $offset = $circunferencia - ($progreso / 100) * $circunferencia;
                        @endphp
                        <svg class="w-28 h-28 sm:w-32 sm:h-32 transform -rotate-90">
                            <circle cx="50%" cy="50%" r="45%" stroke="#e5e7eb" stroke-width="8" fill="none"/>
                            <circle cx="50%" cy="50%" r="45%" stroke="#15803d" stroke-width="8" fill="none"
                                    stroke-dasharray="{{ $circunferencia }}"
                                    stroke-dashoffset="{{ $offset }}"
                                    stroke-linecap="round"
                                    class="transition-all duration-1000 ease-out"/>
                        </svg>
                        <div class="absolute inset-0 flex flex-col items-center justify-center">
                            <span class="text-2xl font-bold text-gray-900">{{ $progreso }}%</span>
                            <span class="text-[10px] text-gray-500 font-medium uppercase tracking-wider">Progreso</span>
                        </div>
                    </div>

                    <div class="flex-1 space-y-1.5 text-sm">
                        <div class="flex items-center gap-2">
                            <span class="iconify w-4 h-4 text-green-700" data-icon="mdi:calendar-start"></span>
                            <span class="text-gray-600">Inicio:</span>
                            <span class="font-medium text-gray-900">
                                {{ $servicioSocial && $servicioSocial->fecha_inicio ? \Carbon\Carbon::parse($servicioSocial->fecha_inicio)->format('d/m/Y') : '—' }}
                            </span>
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="iconify w-4 h-4 text-green-700" data-icon="mdi:calendar-end"></span>
                            <span class="text-gray-600">Finalización:</span>
                            <span class="font-medium text-gray-900">
                                {{ $servicioSocial && $servicioSocial->fecha_limite_segundo_informe ? \Carbon\Carbon::parse($servicioSocial->fecha_limite_segundo_informe)->format('d/m/Y') : '—' }}
                            </span>
                        </div>
                        <div class="flex items-center gap-2 pt-1">
                            @php
                                $estatusSS = $estatusSS ?? 'No solicitado';
                            @endphp
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 text-xs font-medium rounded-full
                                @if($estatusSS == 'Liberado') bg-green-100 text-green-800
                                @elseif($estatusSS == 'Pendiente de revisión') bg-yellow-100 text-yellow-800
                                @elseif($estatusSS == 'En progreso') bg-green-50 text-green-700
                                @elseif($estatusSS == 'Pendiente') bg-gray-100 text-gray-600
                                @else bg-gray-100 text-gray-500 @endif">
                                <span class="iconify w-3 h-3" data-icon="
                                    @if($estatusSS == 'Liberado') mdi:check-decagram
                                    @elseif($estatusSS == 'Pendiente de revisión') mdi:clock-check
                                    @elseif($estatusSS == 'En progreso') mdi:progress-clock
                                    @elseif($estatusSS == 'Pendiente') mdi:clock-outline
                                    @else mdi:clock-outline @endif
                                "></span>
                                {{ $estatusSS }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Prácticas Profesionales -->
        <div class="bg-[#f8f8f8] rounded-xl shadow-md border border-gray-200/80 overflow-hidden transition hover:shadow-lg hover:border-green-200">
            <div class="p-6">
                <div class="flex justify-between items-start mb-4">
                    <div>
                        <div class="flex items-center gap-2">
                            <div class="w-9 h-9 bg-green-50 rounded-lg flex items-center justify-center">
                                <span class="iconify w-5 h-5 text-green-700" data-icon="mdi:briefcase"></span>
                            </div>
                            <div>
                                <h2 class="text-lg font-bold text-gray-900">Prácticas Profesionales</h2>
                                <p class="text-xs text-gray-500">Estado actual de tu trámite</p>
                            </div>
                        </div>
                    </div>
                    <a href="{{ route('practicas.index') }}" class="inline-flex items-center gap-0.5 text-green-700 hover:text-green-800 text-sm font-medium transition group">
                        Ver detalles
                        <span class="iconify w-4 h-4 group-hover:translate-x-0.5 transition" data-icon="mdi:arrow-right"></span>
                    </a>
                </div>

                <div class="flex flex-col sm:flex-row items-center gap-6">
                    <div class="relative flex-shrink-0">
                        @php
                            $progreso = min($progresoPP ?? 0, 100);
                            $circunferencia = 2 * pi() * 45;
                            $offset = $circunferencia - ($progreso / 100) * $circunferencia;
                        @endphp
                        <svg class="w-28 h-28 sm:w-32 sm:h-32 transform -rotate-90">
                            <circle cx="50%" cy="50%" r="45%" stroke="#e5e7eb" stroke-width="8" fill="none"/>
                            <circle cx="50%" cy="50%" r="45%" stroke="#15803d" stroke-width="8" fill="none"
                                    stroke-dasharray="{{ $circunferencia }}"
                                    stroke-dashoffset="{{ $offset }}"
                                    stroke-linecap="round"
                                    class="transition-all duration-1000 ease-out"/>
                        </svg>
                        <div class="absolute inset-0 flex flex-col items-center justify-center">
                            <span class="text-2xl font-bold text-gray-900">{{ $progreso }}%</span>
                            <span class="text-[10px] text-gray-500 font-medium uppercase tracking-wider">Progreso</span>
                        </div>
                    </div>

                    <div class="flex-1 space-y-1.5 text-sm">
                        <div class="flex items-center gap-2">
                            <span class="iconify w-4 h-4 text-green-700" data-icon="mdi:calendar-start"></span>
                            <span class="text-gray-600">Inicio:</span>
                            <span class="font-medium text-gray-900">
                                {{ $practica && $practica->fecha_inicio ? \Carbon\Carbon::parse($practica->fecha_inicio)->format('d/m/Y') : '—' }}
                            </span>
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="iconify w-4 h-4 text-green-700" data-icon="mdi:calendar-end"></span>
                            <span class="text-gray-600">Finalización:</span>
                            <span class="font-medium text-gray-900">
                                {{ $practica && $practica->fecha_limite_final ? \Carbon\Carbon::parse($practica->fecha_limite_final)->format('d/m/Y') : '—' }}
                            </span>
                        </div>
                        <div class="flex items-center gap-2 pt-1">
                            @php
                                $estatusPP = $estatusPP ?? 'No solicitado';
                            @endphp
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 text-xs font-medium rounded-full
                                @if($estatusPP == 'Liberado') bg-green-100 text-green-800
                                @elseif($estatusPP == 'Pendiente de revisión') bg-yellow-100 text-yellow-800
                                @elseif($estatusPP == 'En progreso') bg-green-50 text-green-700
                                @elseif($estatusPP == 'Pendiente') bg-gray-100 text-gray-600
                                @else bg-gray-100 text-gray-500 @endif">
                                <span class="iconify w-3 h-3" data-icon="
                                    @if($estatusPP == 'Liberado') mdi:check-decagram
                                    @elseif($estatusPP == 'Pendiente de revisión') mdi:clock-check
                                    @elseif($estatusPP == 'En progreso') mdi:progress-clock
                                    @elseif($estatusPP == 'Pendiente') mdi:clock-outline
                                    @else mdi:clock-outline @endif
                                "></span>
                                {{ $estatusPP }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- ========================================== -->
    <!-- ANUNCIOS IMPORTANTES - MEJORADO -->
    <!-- ========================================== -->
    <div class="bg-[#f8f8f8] rounded-xl shadow-md border border-gray-200/80 overflow-hidden">
        <div class="p-6">
            <!-- Encabezado de anuncios -->
            <div class="flex items-center justify-between mb-5">
                <div class="flex items-center gap-2">
                    <span class="iconify w-6 h-6 text-green-700" data-icon="mdi:bullhorn"></span>
                    <h3 class="text-lg font-semibold text-gray-900">Anuncios importantes</h3>
                    @if($anuncios->count() > 0)
                        <span class="text-xs bg-green-700 text-white px-2.5 py-0.5 rounded-full font-medium">
                            {{ $anuncios->count() }} {{ $anuncios->count() == 1 ? 'nuevo' : 'nuevos' }}
                        </span>
                    @endif
                </div>
                <span class="text-xs text-gray-400 flex items-center gap-1">
                    <span class="iconify w-3.5 h-3.5" data-icon="mdi:clock-outline"></span>
                    Última actualización: {{ now()->locale('es')->isoFormat('D [de] MMMM [del] YYYY') }}
                </span>
            </div>

            @forelse($anuncios as $anuncio)
                <div class="border-b border-gray-200/60 last:border-0 py-4 hover:bg-white/50 rounded-lg px-4 -mx-4 transition">
                    <div class="flex justify-between items-start gap-4">
                        <div class="flex-1">
                            <div class="flex items-start gap-3">
                                <span class="iconify w-4 h-4 text-green-600 flex-shrink-0 mt-1" data-icon="mdi:message-text"></span>
                                <div>
                                    <p class="text-sm sm:text-base text-gray-700 leading-relaxed">{{ $anuncio->contenido }}</p>
                                    <div class="flex flex-wrap items-center gap-3 mt-2">
                                        <span class="text-xs text-gray-400 flex items-center gap-1">
                                            <span class="iconify w-3 h-3" data-icon="mdi:account-circle"></span>
                                            {{ $anuncio->admin->name }}
                                        </span>
                                        <span class="text-xs text-gray-300">•</span>
                                        <span class="text-xs text-gray-400 flex items-center gap-1">
                                            <span class="iconify w-3 h-3" data-icon="mdi:clock-outline"></span>
                                            Publicado hace {{ $anuncio->created_at->diffForHumans() }}
                                        </span>
                                        <span class="text-xs text-gray-300">•</span>
                                        <span class="text-xs text-gray-400 flex items-center gap-1">
                                            <span class="iconify w-3 h-3" data-icon="mdi:calendar"></span>
                                            {{ $anuncio->created_at->locale('es')->isoFormat('D [de] MMMM [del] YYYY') }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <span class="text-[10px] text-gray-300 flex-shrink-0 font-mono bg-gray-100 px-2 py-0.5 rounded">
                            #{{ $anuncio->id }}
                        </span>
                    </div>
                </div>
            @empty
                <div class="text-center py-12">
                    <span class="iconify w-16 h-16 text-gray-300 mx-auto mb-4" data-icon="mdi:bullhorn-outline"></span>
                    <p class="text-gray-500 text-base font-medium">No hay anuncios disponibles</p>
                    <p class="text-xs text-gray-400 mt-1">Los anuncios importantes aparecerán aquí cuando sean publicados.</p>
                </div>
            @endforelse

            <!-- Pie de anuncios -->
            @if($anuncios->count() > 0)
                <div class="mt-4 pt-3 border-t border-gray-200/60 text-center">
                    <p class="text-[10px] text-gray-400">
                        <span class="iconify w-3 h-3 inline mr-1 align-middle" data-icon="mdi:information-outline"></span>
                        Los anuncios son publicados por el personal administrativo de la institución.
                    </p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection