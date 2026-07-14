@extends('layouts.app')

@section('content')
<div class="max-w-6xl mx-auto px-4 sm:px-6 py-6 sm:py-10">

    <!-- ========================================== -->
    <!-- ENCABEZADO CON BADGE DE ESTATUS -->
    <!-- ========================================== -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-6">
        <div class="flex items-center gap-3">
            <span class="iconify w-9 h-9 sm:w-11 sm:h-11 text-green-700" data-icon="mdi:briefcase-account"></span>
            <div>
                <h1 class="text-2xl sm:text-3xl font-bold text-gray-900">Mi Servicio Social</h1>
                <p class="text-xs text-gray-500 hidden sm:block">Gestiona tu trámite de Servicio Social</p>
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
    <!-- ALERTAS -->
    <!-- ========================================== -->
    @if(session('success'))
        <div class="bg-emerald-50 border-l-4 border-emerald-500 text-emerald-700 p-4 rounded-xl mb-5 flex items-start gap-3 shadow-sm" role="alert">
            <span class="iconify w-5 h-5 flex-shrink-0 mt-0.5 text-emerald-500" data-icon="mdi:check-circle"></span>
            <div class="flex-1 text-sm font-medium">{{ session('success') }}</div>
            <button type="button" class="text-emerald-500 hover:text-emerald-700 transition cursor-pointer" onclick="this.parentElement.remove()">
                <span class="iconify w-5 h-5" data-icon="mdi:close"></span>
            </button>
        </div>
    @endif

    @if(session('error'))
        <div class="bg-rose-50 border-l-4 border-rose-500 text-rose-700 p-4 rounded-xl mb-5 flex items-start gap-3 shadow-sm" role="alert">
            <span class="iconify w-5 h-5 flex-shrink-0 mt-0.5 text-rose-500" data-icon="mdi:alert-circle"></span>
            <div class="flex-1 text-sm font-medium">{{ session('error') }}</div>
            <button type="button" class="text-rose-500 hover:text-rose-700 transition cursor-pointer" onclick="this.parentElement.remove()">
                <span class="iconify w-5 h-5" data-icon="mdi:close"></span>
            </button>
        </div>
    @endif

    @if(session('info'))
        <div class="bg-sky-50 border-l-4 border-sky-500 text-sky-700 p-4 rounded-xl mb-5 flex items-start gap-3 shadow-sm" role="alert">
            <span class="iconify w-5 h-5 flex-shrink-0 mt-0.5 text-sky-500" data-icon="mdi:information"></span>
            <div class="flex-1 text-sm font-medium">{{ session('info') }}</div>
            <button type="button" class="text-sky-500 hover:text-sky-700 transition cursor-pointer" onclick="this.parentElement.remove()">
                <span class="iconify w-5 h-5" data-icon="mdi:close"></span>
            </button>
        </div>
    @endif

    @if($servicioSocial)
        <div class="bg-white rounded-2xl shadow-sm border border-gray-200/80 overflow-hidden transition-all duration-300 hover:shadow-md hover:border-green-200">
            <div class="p-4 sm:p-6">
                
                <!-- ========================================== -->
                <!-- ESTATUS CON TOOLTIP (CENTRADO) -->
                <!-- ========================================== -->
                <div class="flex flex-wrap items-center justify-between mb-4">
                    <div class="flex items-center gap-2">
                        <span class="text-sm font-medium text-gray-700">Estatus:</span>
                        <span class="px-3 py-1 text-xs sm:text-sm rounded-full inline-flex items-center gap-1.5 font-medium
                            @if($servicioSocial->estatus == 'liberado') bg-emerald-100 text-emerald-800
                            @elseif($servicioSocial->estatus == 'en_progreso') bg-blue-100 text-blue-800
                            @elseif($servicioSocial->estatus == 'pendiente') bg-amber-100 text-amber-800
                            @else bg-gray-100 text-gray-500 @endif">
                            @if($servicioSocial->estatus == 'liberado')
                                <span class="iconify w-3.5 h-3.5" data-icon="mdi:check-decagram"></span>
                                Liberado
                            @elseif($servicioSocial->estatus == 'en_progreso')
                                <span class="iconify w-3.5 h-3.5" data-icon="mdi:progress-clock"></span>
                                En progreso
                            @elseif($servicioSocial->estatus == 'pendiente')
                                <span class="iconify w-3.5 h-3.5" data-icon="mdi:clock-outline"></span>
                                Pendiente
                            @else
                                <span class="iconify w-3.5 h-3.5" data-icon="mdi:file-document-outline"></span>
                                No solicitado
                            @endif
                        </span>
                        <!-- Tooltip de estatus: centrado -->
                        <div class="relative inline-block group ml-1">
                            <span class="iconify w-4 h-4 text-gray-400 cursor-help hover:text-green-600 transition" data-icon="mdi:help-circle"></span>
                            <div class="absolute z-50 w-56 bg-gray-800 text-white text-xs rounded-lg p-2 shadow-lg opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200 pointer-events-none
                                        left-1/2 -translate-x-1/2 top-full mt-2">
                                El estatus indica la etapa actual de tu trámite de Servicio Social.
                            </div>
                        </div>
                    </div>
                    <span class="text-xs text-gray-400 flex items-center gap-1">
                        <span class="iconify w-3.5 h-3.5" data-icon="mdi:clock-outline"></span>
                        Actualizado: {{ $servicioSocial->updated_at->diffForHumans() }}
                    </span>
                </div>

                <!-- ========================================== -->
                <!-- FECHAS IMPORTANTES -->
                <!-- ========================================== -->
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 mb-6">
                    @php
                        $fechaInicio = $servicioSocial->fecha_inicio ? \Carbon\Carbon::parse($servicioSocial->fecha_inicio) : null;
                        $fechaPrimer = $servicioSocial->fecha_limite_primer_informe ? \Carbon\Carbon::parse($servicioSocial->fecha_limite_primer_informe) : null;
                        $fechaSegundo = $servicioSocial->fecha_limite_segundo_informe ? \Carbon\Carbon::parse($servicioSocial->fecha_limite_segundo_informe) : null;

                        $colorPrimer = 'border-gray-200/80';
                        if ($fechaPrimer) {
                            $diasPrimer = now()->diffInDays($fechaPrimer, false);
                            if ($diasPrimer < 0) $colorPrimer = 'border-red-300 bg-red-50/30';
                            elseif ($diasPrimer <= 7) $colorPrimer = 'border-amber-300 bg-amber-50/30';
                        }

                        $colorSegundo = 'border-gray-200/80';
                        if ($fechaSegundo) {
                            $diasSegundo = now()->diffInDays($fechaSegundo, false);
                            if ($diasSegundo < 0) $colorSegundo = 'border-red-300 bg-red-50/30';
                            elseif ($diasSegundo <= 7) $colorSegundo = 'border-amber-300 bg-amber-50/30';
                        }
                    @endphp

                    <div class="bg-white rounded-xl p-3 border {{ $colorPrimer }} hover:border-green-200 transition">
                        <p class="text-xs text-gray-500 flex items-center gap-1">
                            <span class="iconify w-3.5 h-3.5 text-green-700" data-icon="mdi:calendar-start"></span>
                            Fecha de inicio
                        </p>
                        <p class="text-sm font-semibold text-gray-900">{{ $fechaInicio ? $fechaInicio->format('d/m/Y') : '—' }}</p>
                    </div>

                    <div class="bg-white rounded-xl p-3 border {{ $colorPrimer }} hover:border-green-200 transition">
                        <p class="text-xs text-gray-500 flex items-center gap-1">
                            <span class="iconify w-3.5 h-3.5 text-amber-600" data-icon="mdi:file-document"></span>
                            Primer informe
                        </p>
                        <p class="text-sm font-semibold text-gray-900">{{ $fechaPrimer ? $fechaPrimer->format('d/m/Y') : '—' }}</p>
                        @if($fechaPrimer && $fechaPrimer->lt(now()))
                            <span class="text-[10px] text-red-500 flex items-center gap-0.5 mt-0.5">
                                <span class="iconify w-3 h-3" data-icon="mdi:alert-circle"></span>
                                Vencido
                            </span>
                        @endif
                    </div>

                    <div class="bg-white rounded-xl p-3 border {{ $colorSegundo }} hover:border-green-200 transition">
                        <p class="text-xs text-gray-500 flex items-center gap-1">
                            <span class="iconify w-3.5 h-3.5 text-amber-600" data-icon="mdi:file-document-multiple"></span>
                            Segundo informe
                        </p>
                        <p class="text-sm font-semibold text-gray-900">{{ $fechaSegundo ? $fechaSegundo->format('d/m/Y') : '—' }}</p>
                        @if($fechaSegundo && $fechaSegundo->lt(now()))
                            <span class="text-[10px] text-red-500 flex items-center gap-0.5 mt-0.5">
                                <span class="iconify w-3 h-3" data-icon="mdi:alert-circle"></span>
                                Vencido
                            </span>
                        @endif
                    </div>

                    <div class="bg-white rounded-xl p-3 border border-gray-200/80 hover:border-green-200 transition">
                        <p class="text-xs text-gray-500 flex items-center gap-1">
                            <span class="iconify w-3.5 h-3.5 text-rose-600" data-icon="mdi:calendar-end"></span>
                            Fecha finalización
                        </p>
                        <p class="text-sm font-semibold text-gray-900">{{ $fechaSegundo ? $fechaSegundo->format('d/m/Y') : '—' }}</p>
                    </div>
                </div>

                <!-- ========================================== -->
                <!-- BARRA DE PROGRESO GENERAL -->
                <!-- ========================================== -->
                @php
                    $progreso = 0;
                    if ($fechaInicio && $fechaSegundo) {
                        $totalDias = $fechaInicio->diffInDays($fechaSegundo);
                        $diasTranscurridos = $fechaInicio->diffInDays(now());
                        if ($diasTranscurridos >= $totalDias) $progreso = 100;
                        elseif ($diasTranscurridos > 0) $progreso = round(($diasTranscurridos / $totalDias) * 100);
                    }
                @endphp
                <div class="mb-6">
                    <div class="flex justify-between text-xs text-gray-500 mb-1">
                        <span>Progreso general</span>
                        <span class="font-medium text-gray-700">{{ $progreso }}%</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2 overflow-hidden">
                        <div class="bg-green-700 h-2 rounded-full transition-all duration-1000 ease-out" style="width: {{ $progreso }}%"></div>
                    </div>
                </div>

                <!-- ========================================== -->
                <!-- DOCUMENTOS CON TOOLTIP (ARRIBA, CENTRADO) -->
                <!-- ========================================== -->
                <div class="mt-4">
                    <div class="flex items-center gap-2 mb-4">
                        <span class="iconify w-5 h-5 text-green-700" data-icon="mdi:file-document-box-multiple"></span>
                        <h3 class="font-semibold text-gray-700 text-sm sm:text-base">Documentos del Servicio Social</h3>
                        <span class="text-xs text-gray-400 font-normal">({{ count($documentosOrdenados ?? []) }})</span>
                        <!-- Tooltip de documentos general: ARRIBA, CENTRADO -->
                        <div class="relative inline-block group">
                            <span class="iconify w-4 h-4 text-gray-400 cursor-help hover:text-green-600 transition" data-icon="mdi:help-circle"></span>
                            <div class="absolute z-50 w-64 bg-gray-800 text-white text-xs rounded-lg p-2 shadow-lg opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200 pointer-events-none
                                        left-1/2 -translate-x-1/2 bottom-full mb-2">
                                <p class="font-medium mb-0.5">📋 Documentos del trámite</p>
                                <p>Sube los documentos requeridos para avanzar en tu Servicio Social.</p>
                                <p class="mt-1 text-gray-300">🔵 <strong>Subir</strong> - Carga tu archivo PDF</p>
                                <p class="text-gray-300">🔵 <strong>Cambiar</strong> - Reemplaza el archivo subido</p>
                                <p class="text-gray-300">🔵 <strong>Eliminar</strong> - Borra el archivo subido</p>
                                <p class="text-gray-300">📥 <strong>Descargar</strong> - Obtén el formato oficial</p>
                            </div>
                        </div>
                    </div>

                    @php
                        $documentosOrdenados = [
                            'Solicitud de Servicio Social' => ['tipo' => 'admin', 'ruta' => 'subir-solicitud', 'descargable' => true, 'tooltip' => 'Descarga el formato de solicitud generado automáticamente con tus datos.'],
                            'Elección de Modalidad' => ['tipo' => 'admin', 'ruta' => 'subir-modalidad', 'descargable' => true, 'tooltip' => 'Descarga el formato de elección de modalidad para seleccionar tu opción.'],
                            'Carta de Presentación de Servicio Social' => ['tipo' => 'admin', 'ruta' => 'subir-carta-presentacion', 'descargable' => true, 'tooltip' => 'Descarga la carta de presentación para entregar en la empresa.'],
                            'Carta de Aceptación' => ['tipo' => 'admin', 'ruta' => 'subir-carta-aceptacion', 'descargable' => false, 'tooltip' => 'Este documento es proporcionado por la empresa. No requiere descarga.'],
                            'Primer Informe de Actividades Trimestral' => ['tipo' => 'informe', 'ruta' => 'subir-reporte-parcial', 'descargable' => true, 'tooltip' => 'Descarga el formato del primer informe de actividades.'],
                            'Segundo Informe de Actividades Trimestral' => ['tipo' => 'informe', 'ruta' => 'subir-reporte-final', 'descargable' => true, 'tooltip' => 'Descarga el formato del segundo informe de actividades.'],
                            'Evaluación de Competencias del Desempeño' => ['tipo' => 'admin', 'ruta' => 'subir-evaluacion', 'descargable' => true, 'tooltip' => 'Descarga el formato de evaluación de competencias.'],
                            'Carta de Liberación de Servicio Social' => ['tipo' => 'admin', 'ruta' => 'subir-liberacion', 'descargable' => false, 'tooltip' => 'Este documento es emitido al finalizar el trámite. No requiere descarga.'],
                        ];

                        $documentos = \App\Models\Documento::where('user_id', Auth::id())
                            ->where('activo', true)
                            ->whereHas('tipoDocumento', fn($q) => $q->where('tramite', 'SS'))
                            ->with('tipoDocumento')
                            ->get()
                            ->keyBy('tipoDocumento.nombre');
                    @endphp

                    <div class="overflow-x-auto">
                        <div class="space-y-2">
                            @foreach($documentosOrdenados as $nombre => $config)
                                @php
                                    $ruta = $config['ruta'];
                                    $descargable = $config['descargable'];
                                    $tooltip = $config['tooltip'];
                                    $doc = $documentos[$nombre] ?? null;
                                    $estaSubido = $doc && $doc->archivo_pdf !== null;
                                    $estatusDoc = $doc ? $doc->estatus : 'pendiente';
                                    $bloqueado = $doc && in_array($doc->estatus, ['validado', 'validado_ventanilla']);

                                    $comentarios = $doc ? $doc->comentarios()->orderBy('created_at', 'desc')->get() : collect();
                                    $tieneComentarios = $comentarios->count() > 0;
                                    $comentariosNoLeidos = $comentarios->filter(fn($c) => $c->tipo == 'admin' && !$c->leido)->count();
                                @endphp

                                <div class="bg-white rounded-xl border border-gray-200/80 hover:border-green-200 hover:shadow-sm transition-all duration-200">
                                    <div class="grid grid-cols-1 md:grid-cols-12 gap-2 items-center p-3">
                                        <!-- Nombre + Tooltip -->
                                        <div class="flex items-center gap-1.5 md:col-span-4">
                                            <div class="relative inline-block group md:order-2 order-1">
                                                <span class="iconify w-3.5 h-3.5 text-gray-400 cursor-help hover:text-green-600 transition" data-icon="mdi:help-circle"></span>
                                                <div class="absolute z-50 w-56 bg-gray-800 text-white text-xs rounded-lg p-2 shadow-lg opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200 pointer-events-none
                                                            left-0 top-full mt-2">
                                                    {{ $tooltip }}
                                                </div>
                                            </div>
                                            <span class="iconify w-5 h-5 text-red-500 flex-shrink-0 order-2 md:order-1" data-icon="mdi:file-pdf-box"></span>
                                            <span class="font-medium text-sm text-gray-800 truncate order-3 md:order-2">{{ $nombre }}</span>
                                        </div>

                                        <!-- Estado -->
                                        <div class="md:col-span-2">
                                            @if($doc && $estaSubido)
                                                @if($estatusDoc == 'validado_ventanilla')
                                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-emerald-100 text-emerald-700 rounded-full text-xs font-medium">
                                                        <span class="iconify w-3.5 h-3.5" data-icon="mdi:check-circle"></span>
                                                        Validado en Ventanilla
                                                    </span>
                                                @elseif($estatusDoc == 'validado')
                                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-blue-100 text-blue-700 rounded-full text-xs font-medium">
                                                        <span class="iconify w-3.5 h-3.5" data-icon="mdi:check-decagram"></span>
                                                        Entregar en Ventanilla
                                                    </span>
                                                @elseif($estatusDoc == 'rechazado')
                                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-rose-100 text-rose-700 rounded-full text-xs font-medium">
                                                        <span class="iconify w-3.5 h-3.5" data-icon="mdi:close-circle"></span>
                                                        Rechazado
                                                    </span>
                                                @else
                                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-amber-100 text-amber-700 rounded-full text-xs font-medium">
                                                        <span class="iconify w-3.5 h-3.5" data-icon="mdi:clock-outline"></span>
                                                        Pendiente
                                                    </span>
                                                @endif
                                            @else
                                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-gray-100 text-gray-500 rounded-full text-xs font-medium">
                                                    <span class="iconify w-3.5 h-3.5" data-icon="mdi:close-circle"></span>
                                                    No subido
                                                </span>
                                            @endif
                                        </div>

                                        <!-- Descarga -->
                                        <div class="md:col-span-2 flex justify-center">
                                            @if($descargable && $servicioSocial->fecha_inicio && $nombre == 'Solicitud de Servicio Social')
                                                <a href="{{ route('servicio-social.word', $servicioSocial->id) }}" 
                                                   class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-medium rounded-lg transition shadow-sm hover:shadow w-full sm:w-auto justify-center">
                                                    <span class="iconify w-4 h-4" data-icon="mdi:download"></span>
                                                    <span>Descargar</span>
                                                </a>
                                            @elseif($descargable && $servicioSocial->fecha_inicio)
                                                <a href="#" 
                                                   class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-medium rounded-lg transition shadow-sm hover:shadow w-full sm:w-auto justify-center opacity-50 cursor-not-allowed">
                                                    <span class="iconify w-4 h-4" data-icon="mdi:download"></span>
                                                    <span>Próximamente</span>
                                                </a>
                                            @else
                                                <span class="text-gray-300 text-xs w-full text-center block">—</span>
                                            @endif
                                        </div>

                                        <!-- Acciones + comentarios (con funcionalidad de leído) -->
                                        <div class="md:col-span-4 flex flex-wrap items-center justify-end gap-1.5">
                                            @if($doc && $doc->archivo_pdf)
                                                @if($bloqueado)
                                                    <span class="inline-flex items-center gap-1 px-2.5 py-1.5 bg-gray-200 text-gray-500 text-xs font-medium rounded-lg cursor-not-allowed">
                                                        <span class="iconify w-3.5 h-3.5" data-icon="mdi:lock"></span>
                                                        <span class="hidden xs:inline">Bloqueado</span>
                                                    </span>
                                                @else
                                                    <a href="{{ route('servicio-social.' . $ruta, $servicioSocial->id) }}" 
                                                       class="inline-flex items-center gap-1 px-2.5 py-1.5 bg-blue-500 hover:bg-blue-600 text-white text-xs font-medium rounded-lg transition shadow-sm hover:shadow">
                                                        <span class="iconify w-3.5 h-3.5" data-icon="mdi:refresh"></span>
                                                        <span class="hidden sm:inline">Cambiar</span>
                                                    </a>
                                                    <button type="button" 
                                                            onclick="mostrarModalEliminar('{{ route('servicio-social.eliminar-documento', [$servicioSocial->id, $nombre]) }}', 'documento')"
                                                            class="inline-flex items-center gap-1 px-2.5 py-1.5 bg-rose-500 hover:bg-rose-600 text-white text-xs font-medium rounded-lg transition shadow-sm hover:shadow">
                                                        <span class="iconify w-3.5 h-3.5" data-icon="mdi:delete"></span>
                                                        <span class="hidden sm:inline">Eliminar</span>
                                                    </button>
                                                @endif
                                            @else
                                                <a href="{{ route('servicio-social.' . $ruta, $servicioSocial->id) }}" 
                                                   class="inline-flex items-center gap-1 px-2.5 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-medium rounded-lg transition shadow-sm hover:shadow">
                                                    <span class="iconify w-3.5 h-3.5" data-icon="mdi:cloud-upload"></span>
                                                    <span class="hidden sm:inline">Subir</span>
                                                </a>
                                            @endif

                                            <!-- Comentarios (con funcionalidad de leído y responsive) -->
                                            @if($tieneComentarios)
                                                <div class="relative inline-block group" 
                                                     data-comentable-type="App\\Models\\Documento"
                                                     data-comentable-id="{{ $doc->id ?? 0 }}">
                                                    <button type="button" 
                                                            class="comentario-btn inline-flex items-center justify-center w-8 h-8 rounded-full hover:bg-gray-100 transition relative"
                                                            onclick="marcarComentariosComoLeidos(this, 'App\\Models\\Documento', {{ $doc->id ?? 0 }}, ['admin'])">
                                                        <span class="iconify w-4 h-4 text-gray-500 group-hover:text-emerald-600 transition" data-icon="mdi:comment-text-outline"></span>
                                                        @if($comentariosNoLeidos > 0)
                                                            <span class="badge-notificacion absolute -top-0.5 -right-0.5 flex items-center justify-center w-4.5 h-4.5 bg-rose-500 text-white text-[9px] font-bold rounded-full animate-pulse shadow-sm">
                                                                {{ $comentariosNoLeidos }}
                                                            </span>
                                                        @endif
                                                    </button>
                                                    <!-- Tooltip de comentarios: en móvil se alinea a la izquierda, en desktop a la derecha -->
                                                    <div class="absolute z-50 w-64 sm:w-80 bg-white rounded-xl shadow-2xl border border-gray-200 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200 pointer-events-none group-hover:pointer-events-auto
                                                                right-0 mt-2 md:right-0 md:left-auto left-0">
                                                        <div class="absolute -top-2 w-3 h-3 bg-white border-l border-t border-gray-200 transform rotate-45
                                                                    right-4 md:right-4 left-auto"></div>
                                                        <div class="p-3 max-h-48 overflow-y-auto space-y-1">
                                                            <div class="flex items-center justify-between mb-2">
                                                                <span class="text-xs font-semibold text-gray-700">Comentarios</span>
                                                                <span class="text-[10px] text-gray-400">{{ $comentarios->count() }}</span>
                                                            </div>
                                                            @foreach($comentarios->take(5) as $c)
                                                                <div class="text-xs {{ str_contains($c->tipo, 'admin') ? 'text-amber-600 bg-amber-50' : 'text-blue-600 bg-blue-50' }} p-2 rounded-lg">
                                                                    <strong>{{ str_contains($c->tipo, 'admin') ? 'Admin' : 'Tú' }}:</strong>
                                                                    {{ \Illuminate\Support\Str::limit($c->contenido, 50) }}
                                                                    <span class="text-[10px] text-gray-400 block mt-0.5">{{ $c->created_at->diffForHumans() }}</span>
                                                                </div>
                                                            @endforeach
                                                            @if($comentarios->count() > 5)
                                                                <div class="text-center text-[10px] text-gray-400 pt-1">+{{ $comentarios->count() - 5 }} más</div>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>
                                            @else
                                                <span class="inline-flex items-center justify-center w-8 h-8 opacity-50">
                                                    <span class="iconify w-4 h-4 text-gray-300" data-icon="mdi:comment-text-outline"></span>
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @else
        <!-- ========================================== -->
        <!-- ESTADO VACÍO -->
        <!-- ========================================== -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-200/80 p-10 text-center">
            <div class="max-w-sm mx-auto">
                <div class="w-24 h-24 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-4">
                    <span class="iconify w-12 h-12 text-gray-300" data-icon="mdi:file-document-outline"></span>
                </div>
                <h3 class="text-lg font-semibold text-gray-800">No hay registro de Servicio Social</h3>
                <p class="text-sm text-gray-500 mt-1">Completa el formulario de solicitud para comenzar tu trámite.</p>
                <a href="{{ route('servicio-social.solicitar') }}" class="inline-flex items-center gap-2 mt-4 px-5 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-medium rounded-lg transition shadow-sm hover:shadow">
                    <span class="iconify w-4 h-4" data-icon="mdi:plus"></span>
                    Iniciar solicitud
                </a>
            </div>
        </div>
    @endif
</div>

<!-- ========================================== -->
<!-- MODAL DE CONFIRMACIÓN PARA ELIMINAR -->
<!-- ========================================== -->
<div id="modalEliminar" class="hidden fixed inset-0 bg-black/30 backdrop-blur-[2px] flex items-center justify-center z-[9999] p-4">
    <div class="bg-white rounded-xl shadow-2xl border border-gray-200 max-w-md w-full p-6 sm:p-8 transform transition-all duration-300 scale-95 opacity-0" id="modalContent">
        <div class="flex justify-center mb-4">
            <div class="w-16 h-16 bg-red-50 rounded-full flex items-center justify-center">
                <span class="iconify w-10 h-10 text-red-500" data-icon="mdi:alert-circle"></span>
            </div>
        </div>
        <h3 class="text-xl font-bold text-gray-900 text-center mb-2">¿Estás seguro?</h3>
        <div class="bg-gray-50 rounded-lg p-4 border border-gray-200 mb-4">
            <p class="text-sm text-gray-700 text-center" id="mensajeModalEliminar">
                Esta acción no se puede deshacer.
            </p>
        </div>
        <div class="flex flex-col sm:flex-row gap-3">
            <button onclick="confirmarEliminar()" class="flex-1 py-2.5 bg-red-600 hover:bg-red-700 text-white font-semibold rounded-lg transition shadow-sm border border-red-700">
                <span class="iconify inline mr-1 align-middle" data-icon="mdi:delete"></span>
                Sí, eliminar
            </button>
            <button onclick="cerrarModalEliminar()" class="flex-1 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold rounded-lg transition shadow-sm">
                <span class="iconify inline mr-1 align-middle" data-icon="mdi:close"></span>
                Cancelar
            </button>
        </div>
    </div>
</div>

<!-- ========================================== -->
<!-- JAVASCRIPT PARA EL MODAL DE ELIMINACIÓN -->
<!-- ========================================== -->
<script>
    let urlEliminar = '';
    let tipoEliminar = '';

    function mostrarModalEliminar(url, tipo) {
        urlEliminar = url;
        tipoEliminar = tipo;
        
        const modal = document.getElementById('modalEliminar');
        const content = document.getElementById('modalContent');
        const mensaje = document.getElementById('mensajeModalEliminar');
        
        if (tipo === 'informe') {
            mensaje.textContent = '¿Estás seguro de eliminar este informe? Esta acción no se puede deshacer.';
        } else {
            mensaje.textContent = '¿Estás seguro de eliminar este documento? Esta acción no se puede deshacer.';
        }
        
        modal.classList.remove('hidden');
        document.body.style.overflow = 'hidden';
        
        setTimeout(() => {
            content.classList.remove('scale-95', 'opacity-0');
            content.classList.add('scale-100', 'opacity-100');
        }, 10);
    }

    function cerrarModalEliminar() {
        const modal = document.getElementById('modalEliminar');
        const content = document.getElementById('modalContent');
        
        content.classList.remove('scale-100', 'opacity-100');
        content.classList.add('scale-95', 'opacity-0');
        
        setTimeout(() => {
            modal.classList.add('hidden');
            document.body.style.overflow = 'auto';
            urlEliminar = '';
        }, 200);
    }

    function confirmarEliminar() {
        if (urlEliminar) {
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = urlEliminar;
            
            const csrf = document.createElement('input');
            csrf.type = 'hidden';
            csrf.name = '_token';
            csrf.value = '{{ csrf_token() }}';
            form.appendChild(csrf);
            
            const method = document.createElement('input');
            method.type = 'hidden';
            method.name = '_method';
            method.value = 'DELETE';
            form.appendChild(method);
            
            document.body.appendChild(form);
            form.submit();
        }
    }

    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            cerrarModalEliminar();
        }
    });

    document.getElementById('modalEliminar').addEventListener('click', function(e) {
        if (e.target === this) {
            cerrarModalEliminar();
        }
    });
</script>

<!-- ========================================== -->
<!-- JAVASCRIPT PARA MARCAR COMENTARIOS COMO LEÍDOS -->
<!-- ========================================== -->
<script>
function marcarComentariosComoLeidos(button, comentableType, comentableId, tipos) {
    if (!comentableId || !tipos || !tipos.length) {
        console.warn('Faltan datos para marcar comentarios');
        return;
    }
    
    const data = {
        comentable_type: comentableType,
        comentable_id: comentableId,
        tipos: tipos,
        _token: '{{ csrf_token() }}'
    };
    
    console.log('📨 Enviando datos:', data);
    
    fetch('{{ route("comentarios.marcar-leidos") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': data._token
        },
        body: JSON.stringify(data)
    })
    .then(response => response.json())
    .then(data => {
        console.log('✅ Respuesta del servidor:', data);
        if (data.success) {
            const badge = button.closest('.group').querySelector('.badge-notificacion');
            if (badge) {
                badge.style.display = 'none';
            }
        }
    })
    .catch(error => {
        console.error('❌ Error al marcar comentarios:', error);
    });
}

document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.group').forEach(function(container) {
        container.addEventListener('mouseenter', function() {
            const button = container.querySelector('.comentario-btn');
            if (button) {
                const badge = container.querySelector('.badge-notificacion');
                if (badge && badge.style.display !== 'none') {
                    // No hacemos nada porque el onclick ya maneja todo
                }
            }
        });
    });
});
</script>

<!-- ========================================== -->
<!-- ESTILOS ADICIONALES -->
<!-- ========================================== -->
<style>
    .group .max-h-48::-webkit-scrollbar {
        width: 3px;
    }
    .group .max-h-48::-webkit-scrollbar-track {
        background: #f1f1f1;
        border-radius: 10px;
    }
    .group .max-h-48::-webkit-scrollbar-thumb {
        background: #d1d5db;
        border-radius: 10px;
    }
    .group .max-h-48::-webkit-scrollbar-thumb:hover {
        background: #9ca3af;
    }
    
    .badge-notificacion {
        transition: all 0.3s ease;
    }
    
    @media (max-width: 640px) {
        .group .absolute {
            right: auto !important;
            left: 0 !important;
        }
        .group .absolute .w-64 {
            width: 280px !important;
        }
        .group .absolute .w-3 {
            right: auto !important;
            left: 12px !important;
        }
    }
    
    #modalContent {
        transition: all 0.2s ease-out;
    }

    .cursor-help {
        cursor: help;
    }
</style>
@endsection