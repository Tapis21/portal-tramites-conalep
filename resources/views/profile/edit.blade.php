@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 py-6 sm:py-10">

    <!-- ========================================== -->
    <!-- ENCABEZADO -->
    <!-- ========================================== -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-8">
        <div>
            <h1 class="text-2xl sm:text-3xl font-bold text-gray-900 flex items-center gap-3">
                <span class="iconify w-8 h-8 sm:w-10 sm:h-10 text-green-800" data-icon="mdi:account-circle"></span>
                Mi Perfil
            </h1>
            <p class="text-sm text-gray-600 mt-1 flex items-center gap-1.5">
                <span class="iconify w-4 h-4" data-icon="mdi:account"></span>
                <strong>{{ Auth::user()->name }} {{ Auth::user()->apellidos }}</strong>
                <span class="text-gray-400 mx-1">•</span>
                <span class="inline-flex items-center gap-1 text-gray-500">
                    <span class="iconify w-3.5 h-3.5" data-icon="mdi:badge-account"></span>
                    {{ Auth::user()->matricula }}
                </span>
            </p>
        </div>
        <div class="mt-3 sm:mt-0">
            <span class="inline-flex items-center gap-2 px-4 py-2 bg-gray-100 border border-gray-200 rounded-lg text-sm text-gray-700">
                <span class="iconify w-4 h-4" data-icon="mdi:clock-outline"></span>
                {{ \Carbon\Carbon::now()->locale('es')->isoFormat('dddd, D [de] MMMM [del] YYYY') }}
            </span>
        </div>
    </div>

    <!-- ========================================== -->
    <!-- ALERTAS DE SESIÓN -->
    <!-- ========================================== -->
    @if(session('status'))
        <div class="bg-green-50 border-l-4 border-green-500 text-green-700 p-4 rounded-lg mb-5 flex items-start gap-3 shadow-sm" role="alert">
            <span class="iconify w-5 h-5 flex-shrink-0 mt-0.5 text-green-500" data-icon="mdi:check-circle"></span>
            <div class="flex-1 text-sm font-medium">
                @if(session('status') == 'profile-updated')
                    Perfil actualizado correctamente.
                @elseif(session('status') == 'password-updated')
                    Contraseña actualizada correctamente.
                @else
                    {{ session('status') }}
                @endif
            </div>
            <button type="button" class="text-green-500 hover:text-green-700 transition cursor-pointer" onclick="this.parentElement.remove()">
                <span class="iconify w-5 h-5" data-icon="mdi:close"></span>
            </button>
        </div>
    @endif

    @if(session('error'))
        <div class="bg-red-50 border-l-4 border-red-500 text-red-700 p-4 rounded-lg mb-5 flex items-start gap-3 shadow-sm" role="alert">
            <span class="iconify w-5 h-5 flex-shrink-0 mt-0.5 text-red-500" data-icon="mdi:alert-circle"></span>
            <div class="flex-1 text-sm font-medium">{{ session('error') }}</div>
            <button type="button" class="text-red-500 hover:text-red-700 transition cursor-pointer" onclick="this.parentElement.remove()">
                <span class="iconify w-5 h-5" data-icon="mdi:close"></span>
            </button>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- ========================================== -->
        <!-- COLUMNA IZQUIERDA: INFORMACIÓN PERSONAL -->
        <!-- ========================================== -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Tarjeta de información personal -->
            <div class="bg-[#f8f8f8] rounded-xl shadow-md border border-gray-200/80 overflow-hidden">
                <div class="p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-semibold text-gray-900 flex items-center gap-2">
                            <span class="iconify w-5 h-5 text-green-700" data-icon="mdi:account-circle"></span>
                            Información personal
                        </h3>
                        <span class="text-xs text-gray-400 flex items-center gap-1">
                            <span class="iconify w-3.5 h-3.5" data-icon="mdi:clock-outline"></span>
                            Última actualización: {{ Auth::user()->updated_at->diffForHumans() }}
                        </span>
                    </div>

                    <!-- Formulario para actualizar perfil -->
                    <form method="POST" action="{{ route('profile.update') }}" class="space-y-4">
                        @csrf
                        @method('patch')

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <!-- Nombre -->
                            <div>
                                <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Nombre</label>
                                <input type="text" name="name" id="name" value="{{ old('name', Auth::user()->name) }}" required
                                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 transition bg-white">
                                @error('name')
                                    <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Apellidos -->
                            <div>
                                <label for="apellidos" class="block text-sm font-medium text-gray-700 mb-1">Apellidos</label>
                                <input type="text" name="apellidos" id="apellidos" value="{{ old('apellidos', Auth::user()->apellidos) }}" required
                                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 transition bg-white">
                                @error('apellidos')
                                    <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Email -->
                            <div>
                                <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Correo electrónico</label>
                                <input type="email" name="email" id="email" value="{{ old('email', Auth::user()->email) }}" required
                                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 transition bg-white">
                                @error('email')
                                    <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Matrícula (solo lectura) -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Matrícula</label>
                                <div class="w-full px-3 py-2 bg-gray-100 border border-gray-200 rounded-lg text-gray-600 cursor-not-allowed">
                                    {{ Auth::user()->matricula }}
                                </div>
                            </div>

                            <!-- Carrera (solo lectura) -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Carrera</label>
                                <div class="w-full px-3 py-2 bg-gray-100 border border-gray-200 rounded-lg text-gray-600 cursor-not-allowed">
                                    {{ Auth::user()->carrera }}
                                </div>
                            </div>

                            <!-- Semestre (solo lectura) -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Semestre</label>
                                <div class="w-full px-3 py-2 bg-gray-100 border border-gray-200 rounded-lg text-gray-600 cursor-not-allowed">
                                    {{ Auth::user()->semestre }}° Semestre
                                </div>
                            </div>

                            <!-- Grupo (solo lectura) -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Grupo</label>
                                <div class="w-full px-3 py-2 bg-gray-100 border border-gray-200 rounded-lg text-gray-600 cursor-not-allowed">
                                    {{ Auth::user()->grupo ?? 'No asignado' }}
                                </div>
                            </div>

                            <!-- Turno (solo lectura) -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Turno</label>
                                <div class="w-full px-3 py-2 bg-gray-100 border border-gray-200 rounded-lg text-gray-600 cursor-not-allowed">
                                    {{ Auth::user()->nombre_turno }}
                                </div>
                            </div>

                            <!-- Periodo actual (solo lectura) -->
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Periodo actual</label>
                                <div class="w-full px-3 py-2 bg-gray-100 border border-gray-200 rounded-lg text-gray-600 cursor-not-allowed flex items-center justify-between">
                                    <span>{{ Auth::user()->periodoActual() ? Auth::user()->periodoActual()->nombre : 'Sin periodo activo' }}</span>
                                    @if(Auth::user()->periodoActual())
                                        <span class="inline-flex items-center gap-1 text-xs text-green-700 bg-green-50 px-2 py-0.5 rounded-full">
                                            <span class="iconify w-3 h-3" data-icon="mdi:check-circle"></span>
                                            Activo
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1 text-xs text-amber-600 bg-amber-50 px-2 py-0.5 rounded-full">
                                            <span class="iconify w-3 h-3" data-icon="mdi:alert-circle"></span>
                                            Inactivo
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="pt-2">
                            <button type="submit" class="inline-flex items-center gap-2 px-6 py-2.5 bg-green-700 hover:bg-green-800 text-white text-sm font-medium rounded-lg transition shadow-sm border border-green-800">
                                <span class="iconify w-4 h-4" data-icon="mdi:content-save"></span>
                                Actualizar perfil
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- ========================================== -->
            <!-- SERVICIO SOCIAL - VISTA PREVIA -->
            <!-- ========================================== -->
            @php
                $servicioSocial = Auth::user()->servicioSocial;
                $tieneSS = $servicioSocial && $servicioSocial->fecha_inicio;
                $estatusSS = $tieneSS ? $servicioSocial->estatus : null;
            @endphp

            <div class="bg-[#f8f8f8] rounded-xl shadow-md border border-gray-200/80 overflow-hidden">
                <div class="p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-semibold text-gray-900 flex items-center gap-2">
                            <span class="iconify w-5 h-5 text-green-700" data-icon="mdi:briefcase-account"></span>
                            Servicio Social
                        </h3>
                        <a href="{{ route('servicio-social.index') }}" class="inline-flex items-center gap-1 text-sm text-green-700 hover:text-green-800 transition">
                            Ver detalles
                            <span class="iconify w-4 h-4" data-icon="mdi:arrow-right"></span>
                        </a>
                    </div>

                    @if($tieneSS)
                        @php
                            $progresoSS = 0;
                            if ($servicioSocial->fecha_limite_segundo_informe) {
                                $inicio = \Carbon\Carbon::parse($servicioSocial->fecha_inicio);
                                $fin = \Carbon\Carbon::parse($servicioSocial->fecha_limite_segundo_informe);
                                $hoy = now();
                                if ($hoy->gte($fin)) {
                                    $progresoSS = 100;
                                } elseif ($hoy->lte($inicio)) {
                                    $progresoSS = 0;
                                } else {
                                    $totalDias = $inicio->diffInDays($fin);
                                    $diasTranscurridos = $inicio->diffInDays($hoy);
                                    $progresoSS = round(($diasTranscurridos / $totalDias) * 100);
                                }
                            }

                            $estatusLabel = match($estatusSS) {
                                'liberado' => ['label' => 'Liberado', 'color' => 'bg-green-100 text-green-800', 'icon' => 'mdi:check-decagram'],
                                'pendiente_revision' => ['label' => 'Pendiente de revisión', 'color' => 'bg-yellow-100 text-yellow-800', 'icon' => 'mdi:clock-check'],
                                'en_progreso' => ['label' => 'En progreso', 'color' => 'bg-green-50 text-green-700', 'icon' => 'mdi:progress-clock'],
                                'pendiente' => ['label' => 'Pendiente', 'color' => 'bg-gray-100 text-gray-600', 'icon' => 'mdi:clock-outline'],
                                default => ['label' => 'No solicitado', 'color' => 'bg-gray-100 text-gray-500', 'icon' => 'mdi:clock-outline'],
                            };
                        @endphp
                        <div class="flex flex-col sm:flex-row items-start sm:items-center gap-4">
                            <div class="relative flex-shrink-0">
                                @php
                                    $circunferencia = 2 * pi() * 30;
                                    $offset = $circunferencia - ($progresoSS / 100) * $circunferencia;
                                @endphp
                                <svg class="w-20 h-20 transform -rotate-90">
                                    <circle cx="50%" cy="50%" r="30" stroke="#e5e7eb" stroke-width="6" fill="none"/>
                                    @if($progresoSS > 0)
                                        <circle cx="50%" cy="50%" r="30" stroke="#15803d" stroke-width="6" fill="none"
                                                stroke-dasharray="{{ $circunferencia }}"
                                                stroke-dashoffset="{{ $offset }}"
                                                stroke-linecap="butt"
                                                class="transition-all duration-1000 ease-out"/>
                                    @endif
                                </svg>
                                <div class="absolute inset-0 flex items-center justify-center">
                                    <span class="text-xs font-bold text-gray-900">{{ $progresoSS }}%</span>
                                </div>
                            </div>

                            <div class="flex-1 space-y-1 text-sm">
                                <div class="flex flex-wrap items-center gap-3">
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 text-xs font-medium rounded-full {{ $estatusLabel['color'] }}">
                                        <span class="iconify w-3 h-3" data-icon="{{ $estatusLabel['icon'] }}"></span>
                                        {{ $estatusLabel['label'] }}
                                    </span>
                                    <span class="text-xs text-gray-400 flex items-center gap-1">
                                        <span class="iconify w-3 h-3" data-icon="mdi:calendar-start"></span>
                                        Inicio: {{ \Carbon\Carbon::parse($servicioSocial->fecha_inicio)->format('d/m/Y') }}
                                    </span>
                                    <span class="text-xs text-gray-400 flex items-center gap-1">
                                        <span class="iconify w-3 h-3" data-icon="mdi:calendar-end"></span>
                                        Fin: {{ \Carbon\Carbon::parse($servicioSocial->fecha_limite_segundo_informe)->format('d/m/Y') }}
                                    </span>
                                </div>
                                <div class="w-full bg-gray-200 rounded-full h-1.5">
                                    <div class="bg-green-700 h-1.5 rounded-full transition-all duration-1000" style="width: {{ $progresoSS }}%"></div>
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="text-center py-6">
                            <span class="iconify w-12 h-12 text-gray-300 mx-auto mb-2" data-icon="mdi:briefcase-account-outline"></span>
                            <p class="text-gray-500 text-sm">No has solicitado Servicio Social</p>
                            <a href="{{ route('servicio-social.index') }}" class="inline-flex items-center gap-1 mt-2 text-sm text-green-700 hover:text-green-800 transition">
                                Iniciar solicitud
                                <span class="iconify w-4 h-4" data-icon="mdi:arrow-right"></span>
                            </a>
                        </div>
                    @endif
                </div>
            </div>

            <!-- ========================================== -->
            <!-- PRÁCTICAS - VISTA PREVIA -->
            <!-- ========================================== -->
            @php
                $practica = Auth::user()->practicas;
                $tienePP = $practica && $practica->fecha_inicio;
                $estatusPP = $tienePP ? $practica->estatus : null;
            @endphp

            <div class="bg-[#f8f8f8] rounded-xl shadow-md border border-gray-200/80 overflow-hidden">
                <div class="p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-semibold text-gray-900 flex items-center gap-2">
                            <span class="iconify w-5 h-5 text-green-700" data-icon="mdi:briefcase"></span>
                            Prácticas Profesionales
                        </h3>
                        <a href="{{ route('practicas.index') }}" class="inline-flex items-center gap-1 text-sm text-green-700 hover:text-green-800 transition">
                            Ver detalles
                            <span class="iconify w-4 h-4" data-icon="mdi:arrow-right"></span>
                        </a>
                    </div>

                    @if($tienePP)
                        @php
                            $progresoPP = 0;
                            if ($practica->fecha_limite_final) {
                                $inicio = \Carbon\Carbon::parse($practica->fecha_inicio);
                                $fin = \Carbon\Carbon::parse($practica->fecha_limite_final);
                                $hoy = now();
                                if ($hoy->gte($fin)) {
                                    $progresoPP = 100;
                                } elseif ($hoy->lte($inicio)) {
                                    $progresoPP = 0;
                                } else {
                                    $totalDias = $inicio->diffInDays($fin);
                                    $diasTranscurridos = $inicio->diffInDays($hoy);
                                    $progresoPP = round(($diasTranscurridos / $totalDias) * 100);
                                }
                            }

                            $estatusLabel = match($estatusPP) {
                                'liberado' => ['label' => 'Liberado', 'color' => 'bg-green-100 text-green-800', 'icon' => 'mdi:check-decagram'],
                                'pendiente_revision' => ['label' => 'Pendiente de revisión', 'color' => 'bg-yellow-100 text-yellow-800', 'icon' => 'mdi:clock-check'],
                                'en_progreso' => ['label' => 'En progreso', 'color' => 'bg-green-50 text-green-700', 'icon' => 'mdi:progress-clock'],
                                'pendiente' => ['label' => 'Pendiente', 'color' => 'bg-gray-100 text-gray-600', 'icon' => 'mdi:clock-outline'],
                                default => ['label' => 'No solicitado', 'color' => 'bg-gray-100 text-gray-500', 'icon' => 'mdi:clock-outline'],
                            };
                        @endphp
                        <div class="flex flex-col sm:flex-row items-start sm:items-center gap-4">
                            <div class="relative flex-shrink-0">
                                @php
                                    $circunferencia = 2 * pi() * 30;
                                    $offset = $circunferencia - ($progresoPP / 100) * $circunferencia;
                                @endphp
                                <svg class="w-20 h-20 transform -rotate-90">
                                    <circle cx="50%" cy="50%" r="30" stroke="#e5e7eb" stroke-width="6" fill="none"/>
                                    @if($progresoPP > 0)
                                        <circle cx="50%" cy="50%" r="30" stroke="#15803d" stroke-width="6" fill="none"
                                                stroke-dasharray="{{ $circunferencia }}"
                                                stroke-dashoffset="{{ $offset }}"
                                                stroke-linecap="butt"
                                                class="transition-all duration-1000 ease-out"/>
                                    @endif
                                </svg>
                                <div class="absolute inset-0 flex items-center justify-center">
                                    <span class="text-xs font-bold text-gray-900">{{ $progresoPP }}%</span>
                                </div>
                            </div>

                            <div class="flex-1 space-y-1 text-sm">
                                <div class="flex flex-wrap items-center gap-3">
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 text-xs font-medium rounded-full {{ $estatusLabel['color'] }}">
                                        <span class="iconify w-3 h-3" data-icon="{{ $estatusLabel['icon'] }}"></span>
                                        {{ $estatusLabel['label'] }}
                                    </span>
                                    <span class="text-xs text-gray-400 flex items-center gap-1">
                                        <span class="iconify w-3 h-3" data-icon="mdi:calendar-start"></span>
                                        Inicio: {{ \Carbon\Carbon::parse($practica->fecha_inicio)->format('d/m/Y') }}
                                    </span>
                                    <span class="text-xs text-gray-400 flex items-center gap-1">
                                        <span class="iconify w-3 h-3" data-icon="mdi:calendar-end"></span>
                                        Fin: {{ \Carbon\Carbon::parse($practica->fecha_limite_final)->format('d/m/Y') }}
                                    </span>
                                </div>
                                <div class="w-full bg-gray-200 rounded-full h-1.5">
                                    <div class="bg-green-700 h-1.5 rounded-full transition-all duration-1000" style="width: {{ $progresoPP }}%"></div>
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="text-center py-6">
                            <span class="iconify w-12 h-12 text-gray-300 mx-auto mb-2" data-icon="mdi:briefcase-outline"></span>
                            <p class="text-gray-500 text-sm">No has solicitado Prácticas Profesionales</p>
                            <a href="{{ route('practicas.index') }}" class="inline-flex items-center gap-1 mt-2 text-sm text-green-700 hover:text-green-800 transition">
                                Iniciar solicitud
                                <span class="iconify w-4 h-4" data-icon="mdi:arrow-right"></span>
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- ========================================== -->
        <!-- COLUMNA DERECHA: SEGURIDAD Y ACCIONES -->
        <!-- ========================================== -->
        <div class="space-y-6">
            <!-- Tarjeta de seguridad -->
            <div class="bg-[#f8f8f8] rounded-xl shadow-md border border-gray-200/80 overflow-hidden">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-900 flex items-center gap-2 mb-4">
                        <span class="iconify w-5 h-5 text-green-700" data-icon="mdi:security"></span>
                        Seguridad
                    </h3>

                    <!-- Último cambio de contraseña -->
                    <div class="bg-white rounded-lg p-3 border border-gray-200/80 mb-4">
                        <div class="flex items-center justify-between">
                            <div>
                                <label class="block text-xs text-gray-500 font-medium uppercase tracking-wider mb-0.5">Último cambio de contraseña</label>
                                <p class="text-sm font-medium text-gray-900">
                                    @if(Auth::user()->password_changed_at)
                                        {{ \Carbon\Carbon::parse(Auth::user()->password_changed_at)->format('d/m/Y H:i') }}
                                        <span class="text-xs text-gray-400 ml-1">({{ \Carbon\Carbon::parse(Auth::user()->password_changed_at)->diffForHumans() }})</span>
                                    @else
                                        <span class="text-amber-600">Nunca se ha cambiado</span>
                                    @endif
                                </p>
                            </div>
                            @if(Auth::user()->password_changed_at)
                                <span class="inline-flex items-center gap-1 text-xs text-green-700 bg-green-50 px-2 py-0.5 rounded-full">
                                    <span class="iconify w-3.5 h-3.5" data-icon="mdi:check-circle"></span>
                                    Actualizada
                                </span>
                            @endif
                        </div>
                    </div>

                    <!-- Botón para cambiar contraseña -->
                    <button onclick="abrirModalPassword()" 
                            class="w-full flex items-center justify-center gap-2 px-4 py-2.5 bg-green-700 hover:bg-green-800 text-white text-sm font-medium rounded-lg transition shadow-sm border border-green-800">
                        <span class="iconify w-4 h-4" data-icon="mdi:key-change"></span>
                        Cambiar contraseña
                    </button>
                </div>
            </div>

            <!-- Tarjeta de acciones -->
            <div class="bg-[#f8f8f8] rounded-xl shadow-md border border-gray-200/80 overflow-hidden">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-900 flex items-center gap-2 mb-4">
                        <span class="iconify w-5 h-5 text-green-700" data-icon="mdi:exit-to-app"></span>
                        Acciones
                    </h3>
                    <a href="{{ route('dashboard') }}" class="flex items-center gap-2 px-4 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-medium rounded-lg transition w-full justify-center">
                        <span class="iconify w-4 h-4" data-icon="mdi:view-dashboard"></span>
                        Ir al Dashboard
                    </a>
                    <form method="POST" action="{{ route('logout') }}" class="mt-2">
                        @csrf
                        <button type="submit" class="w-full flex items-center justify-center gap-2 px-4 py-2.5 bg-red-50 hover:bg-red-100 text-red-600 text-sm font-medium rounded-lg transition border border-red-200">
                            <span class="iconify w-4 h-4" data-icon="mdi:logout"></span>
                            Cerrar sesión
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- ========================================== -->
<!-- MODAL PARA CAMBIAR CONTRASEÑA -->
<!-- ========================================== -->
<div id="modalCambiarPassword" class="hidden fixed inset-0 bg-black/30 backdrop-blur-[2px] flex items-center justify-center z-[9999] p-4">
    <div class="bg-white rounded-xl shadow-2xl border border-gray-200 max-w-md w-full p-6 sm:p-8 transform transition-all duration-300 scale-95 opacity-0" id="modalPasswordContent">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-bold text-gray-900 flex items-center gap-2">
                <span class="iconify w-5 h-5 text-green-700" data-icon="mdi:key-change"></span>
                Cambiar contraseña
            </h3>
            <button onclick="cerrarModalPassword()" class="text-gray-400 hover:text-gray-600 transition">
                <span class="iconify w-6 h-6" data-icon="mdi:close"></span>
            </button>
        </div>

        <form method="POST" action="{{ route('password.update') }}" class="space-y-4">
            @csrf
            @method('put')

            <div>
                <label for="current_password" class="block text-sm font-medium text-gray-700 mb-1">Contraseña actual</label>
                <input type="password" name="current_password" id="current_password" required
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 transition bg-white">
                @error('current_password')
                    <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Nueva contraseña</label>
                <input type="password" name="password" id="password" required
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 transition bg-white">
                @error('password')
                    <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-1">Confirmar nueva contraseña</label>
                <input type="password" name="password_confirmation" id="password_confirmation" required
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 transition bg-white">
            </div>

            <div class="flex gap-3 pt-2">
                <button type="submit" class="flex-1 py-2.5 bg-green-700 hover:bg-green-800 text-white font-semibold rounded-lg transition shadow-sm border border-green-800">
                    <span class="iconify inline mr-1 align-middle" data-icon="mdi:check"></span>
                    Actualizar
                </button>
                <button type="button" onclick="cerrarModalPassword()" class="flex-1 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold rounded-lg transition shadow-sm">
                    <span class="iconify inline mr-1 align-middle" data-icon="mdi:close"></span>
                    Cancelar
                </button>
            </div>
        </form>
    </div>
</div>

<!-- ========================================== -->
<!-- JAVASCRIPT PARA EL MODAL -->
<!-- ========================================== -->
<script>
    function cerrarModalPassword() {
        const modal = document.getElementById('modalCambiarPassword');
        const content = document.getElementById('modalPasswordContent');
        content.classList.remove('scale-100', 'opacity-100');
        content.classList.add('scale-95', 'opacity-0');
        setTimeout(() => {
            modal.classList.add('hidden');
            document.body.style.overflow = 'auto';
        }, 200);
    }

    function abrirModalPassword() {
        const modal = document.getElementById('modalCambiarPassword');
        const content = document.getElementById('modalPasswordContent');
        modal.classList.remove('hidden');
        document.body.style.overflow = 'hidden';
        setTimeout(() => {
            content.classList.remove('scale-95', 'opacity-0');
            content.classList.add('scale-100', 'opacity-100');
        }, 10);
    }

    document.getElementById('modalCambiarPassword').addEventListener('click', function(e) {
        if (e.target === this) {
            cerrarModalPassword();
        }
    });

    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            cerrarModalPassword();
        }
    });
</script>
@endsection