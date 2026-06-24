@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 py-6 sm:py-10">

    <!-- Botón Atrás (solo visible en móvil) -->
    <div class="sm:hidden mb-4">
        <a href="{{ route('dashboard') }}" class="inline-flex items-center gap-2 text-green-700 hover:text-green-900 transition text-sm font-medium">
            <span class="iconify w-5 h-5" data-icon="mdi:arrow-left-circle"></span>
            Volver al inicio
        </a>
    </div>

    <!-- Título -->
    <div class="flex items-center gap-3 mb-6">
        <span class="iconify w-8 h-8 sm:w-10 sm:h-10 text-green-700" data-icon="mdi:briefcase"></span>
        <h1 class="text-xl sm:text-2xl font-bold text-gray-900">Mis Prácticas Profesionales</h1>
    </div>

    <!-- ========================================== -->
    <!-- ALERTAS MEJORADAS -->
    <!-- ========================================== -->
    @if(session('success'))
        <div class="bg-green-50 border-l-4 border-green-500 text-green-700 p-4 rounded-md mb-4 flex items-start gap-3 shadow-sm" role="alert">
            <span class="iconify w-5 h-5 flex-shrink-0 mt-0.5 text-green-500" data-icon="mdi:check-circle"></span>
            <div class="flex-1">
                <p class="text-sm font-medium">{{ session('success') }}</p>
            </div>
            <button type="button" class="text-green-500 hover:text-green-700 transition cursor-pointer" onclick="this.parentElement.remove()">
                <span class="iconify w-5 h-5" data-icon="mdi:close"></span>
            </button>
        </div>
    @endif

    @if(session('error'))
        <div class="bg-red-50 border-l-4 border-red-500 text-red-700 p-4 rounded-md mb-4 flex items-start gap-3 shadow-sm" role="alert">
            <span class="iconify w-5 h-5 flex-shrink-0 mt-0.5 text-red-500" data-icon="mdi:alert-circle"></span>
            <div class="flex-1">
                <p class="text-sm font-medium">{{ session('error') }}</p>
            </div>
            <button type="button" class="text-red-500 hover:text-red-700 transition cursor-pointer" onclick="this.parentElement.remove()">
                <span class="iconify w-5 h-5" data-icon="mdi:close"></span>
            </button>
        </div>
    @endif

    @if(session('info'))
        <div class="bg-blue-50 border-l-4 border-blue-500 text-blue-700 p-4 rounded-md mb-4 flex items-start gap-3 shadow-sm" role="alert">
            <span class="iconify w-5 h-5 flex-shrink-0 mt-0.5 text-blue-500" data-icon="mdi:information"></span>
            <div class="flex-1">
                <p class="text-sm font-medium">{{ session('info') }}</p>
            </div>
            <button type="button" class="text-blue-500 hover:text-blue-700 transition cursor-pointer" onclick="this.parentElement.remove()">
                <span class="iconify w-5 h-5" data-icon="mdi:close"></span>
            </button>
        </div>
    @endif

    @if($practica)
        <div class="bg-[#f8f8f8] rounded-xl shadow-md border border-gray-200/80 overflow-hidden transition hover:shadow-lg hover:border-green-200">
            <div class="p-4 sm:p-6">
                <!-- Fechas importantes -->
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 mb-6">
                    <div class="bg-white rounded-lg p-2 sm:p-3 border border-gray-200/80 hover:border-green-200 transition">
                        <p class="text-xs text-gray-500 flex items-center gap-1">
                            <span class="iconify w-3 h-3 text-green-700" data-icon="mdi:calendar-start"></span>
                            Fecha de inicio
                        </p>
                        <p class="text-sm sm:text-base font-semibold text-gray-900">{{ $practica->fecha_inicio ? \Carbon\Carbon::parse($practica->fecha_inicio)->format('d/m/Y') : 'No definida' }}</p>
                    </div>
                    <div class="bg-white rounded-lg p-2 sm:p-3 border border-gray-200/80 hover:border-green-200 transition">
                        <p class="text-xs text-gray-500 flex items-center gap-1">
                            <span class="iconify w-3 h-3 text-green-700" data-icon="mdi:file-document"></span>
                            Primer informe disponible
                        </p>
                        <p class="text-sm sm:text-base font-semibold text-gray-900">{{ $practica->fecha_limite_parcial ? \Carbon\Carbon::parse($practica->fecha_limite_parcial)->format('d/m/Y') : 'No definida' }}</p>
                    </div>
                    <div class="bg-white rounded-lg p-2 sm:p-3 border border-gray-200/80 hover:border-green-200 transition">
                        <p class="text-xs text-gray-500 flex items-center gap-1">
                            <span class="iconify w-3 h-3 text-green-700" data-icon="mdi:file-document-multiple"></span>
                            Segundo informe disponible
                        </p>
                        <p class="text-sm sm:text-base font-semibold text-gray-900">{{ $practica->fecha_limite_final ? \Carbon\Carbon::parse($practica->fecha_limite_final)->format('d/m/Y') : 'No definida' }}</p>
                    </div>
                    <div class="bg-white rounded-lg p-2 sm:p-3 border border-gray-200/80 hover:border-green-200 transition">
                        <p class="text-xs text-gray-500 flex items-center gap-1">
                            <span class="iconify w-3 h-3 text-green-700" data-icon="mdi:calendar-end"></span>
                            Fecha de finalización
                        </p>
                        <p class="text-sm sm:text-base font-semibold text-gray-900">{{ $practica->fecha_limite_final ? \Carbon\Carbon::parse($practica->fecha_limite_final)->format('d/m/Y') : 'No definida' }}</p>
                    </div>
                </div>

                <!-- Estatus -->
                <p class="mb-4">
                    <strong class="text-sm sm:text-base text-gray-700">Estatus:</strong>
                    <span class="px-2 sm:px-3 py-1 text-xs sm:text-sm rounded-full inline-flex items-center gap-1
                        @if($practica->estatus == 'liberado') bg-green-100 text-green-800
                        @elseif($practica->estatus == 'en_progreso') bg-blue-100 text-blue-800
                        @elseif($practica->estatus == 'pendiente') bg-yellow-100 text-yellow-800
                        @else bg-gray-100 text-gray-500 @endif">
                        
                        @if($practica->estatus == 'liberado')
                            <span class="iconify w-3 h-3 sm:w-4 sm:h-4" data-icon="mdi:check-decagram"></span>
                            Liberado
                        @elseif($practica->estatus == 'en_progreso')
                            <span class="iconify w-3 h-3 sm:w-4 sm:h-4" data-icon="mdi:progress-clock"></span>
                            En progreso
                        @elseif($practica->estatus == 'pendiente')
                            <span class="iconify w-3 h-3 sm:w-4 sm:h-4" data-icon="mdi:clock-outline"></span>
                            Pendiente
                        @else
                            <span class="iconify w-3 h-3 sm:w-4 sm:h-4" data-icon="mdi:file-document-outline"></span>
                            No solicitado
                        @endif
                    </span>
                </p>

                <!-- Documentos de Prácticas Profesionales -->
                <div class="mt-6">
                    <h3 class="font-semibold text-gray-700 text-sm sm:text-base mb-4 flex items-center gap-2">
                        <span class="iconify w-5 h-5 text-green-700" data-icon="mdi:file-document-box-multiple"></span>
                        Documentos de Prácticas Profesionales
                    </h3>
                    
                    @php
                        $documentosOrdenados = [
                            'Solicitud de Prácticas Profesionales' => ['tipo' => 'admin', 'ruta' => 'subir-solicitud', 'campo' => null],
                            'Elección de Modalidad' => ['tipo' => 'admin', 'ruta' => 'subir-modalidad', 'campo' => null],
                            'Carta de Presentación de Prácticas Profesionales' => ['tipo' => 'admin', 'ruta' => 'subir-carta-presentacion', 'campo' => null],
                            'Carta de Aceptación' => ['tipo' => 'admin', 'ruta' => 'subir-carta-aceptacion', 'campo' => null],
                            'Primer Informe de Actividades' => ['tipo' => 'informe', 'ruta' => 'subir-reporte-parcial', 'campo' => 'reporte_parcial_subido'],
                            'Segundo Informe de Actividades' => ['tipo' => 'informe', 'ruta' => 'subir-reporte-final', 'campo' => 'reporte_final_subido'],
                            'Evaluación de Competencias del Desempeño' => ['tipo' => 'admin', 'ruta' => 'subir-evaluacion', 'campo' => null],
                            'Carta de Liberación de Prácticas Profesionales' => ['tipo' => 'admin', 'ruta' => 'subir-liberacion', 'campo' => null],
                        ];
                        
                        $subidos = \App\Models\Documento::where('user_id', Auth::id())
                            ->whereHas('tipoDocumento', function($q) use ($documentosOrdenados) {
                                $q->whereIn('nombre', array_keys($documentosOrdenados))
                                ->where('tramite', 'PP');
                            })
                            ->where('activo', true)
                            ->with('tipoDocumento')
                            ->get()
                            ->pluck('tipoDocumento.nombre')
                            ->toArray();
                    @endphp
                    
                    <div class="overflow-x-auto">
                        <div class="md:min-w-full">
                            <!-- Encabezados -->
                            <div class="hidden md:grid md:grid-cols-12 gap-4 px-3 py-2 bg-white border border-gray-200/80 rounded-t-lg text-xs font-semibold text-gray-600 mb-2">
                                <div class="col-span-6">Documento</div>
                                <div class="col-span-3">Estado</div>
                                <div class="col-span-2">Descarga</div>
                                <div class="col-span-1 text-right">Acciones</div>
                            </div>
                            
                            <div class="space-y-2">
                                @foreach($documentosOrdenados as $nombre => $config)
                                    @php
                                        $ruta = $config['ruta'];
                                        $estaSubido = false;
                                        $estaValidado = false;
                                        
                                        if ($config['tipo'] == 'admin') {
                                            $docActual = \App\Models\Documento::where('user_id', Auth::id())
                                                ->whereHas('tipoDocumento', function($q) use ($nombre) {
                                                    $q->where('nombre', $nombre)
                                                      ->where('tramite', 'PP');
                                                })->first();
                                            
                                            $estaSubido = $docActual && $docActual->archivo_pdf !== null;
                                            $estaValidado = $docActual && $docActual->estatus == 'validado';
                                            $doc = $docActual;
                                        } else {
                                            $campo = $config['campo'];
                                            $estaSubido = $practica->$campo ?? false;
                                            if ($nombre == 'Primer Informe de Actividades') {
                                                $estaValidado = $practica->reporte_parcial_validado ?? false;
                                            } else {
                                                $estaValidado = $practica->reporte_final_validado ?? false;
                                            }
                                            $doc = null;
                                        }
                                        
                                        // Obtener comentarios para mostrar en el tooltip
                                        $comentariosTooltip = collect();
                                        if ($config['tipo'] == 'admin' && $doc) {
                                            $comentariosTooltip = $doc->comentarios()->orderBy('created_at', 'desc')->get();
                                        } elseif ($config['tipo'] == 'informe') {
                                            if ($nombre == 'Primer Informe de Actividades') {
                                                $tiposComentarios = ['estudiante_primer_informe', 'admin_primer_informe'];
                                            } else {
                                                $tiposComentarios = ['estudiante_segundo_informe', 'admin_segundo_informe'];
                                            }
                                            $comentariosTooltip = \App\Models\Comentario::where('comentable_type', 'App\Models\Practica')
                                                ->where('comentable_id', $practica->id)
                                                ->whereIn('tipo', $tiposComentarios)
                                                ->orderBy('created_at', 'desc')
                                                ->get();
                                        }
                                        
                                        $tieneComentarios = $comentariosTooltip->count() > 0;
                                        $comentariosNoLeidos = 0;
                                        foreach($comentariosTooltip as $c) {
                                            if($c->tipo == 'admin' && !$c->leido) {
                                                $comentariosNoLeidos++;
                                            }
                                        }
                                        
                                        $tiposParaMarcar = [];
                                        if ($config['tipo'] == 'admin' && $doc) {
                                            $tiposParaMarcar = ['admin'];
                                        } elseif ($config['tipo'] == 'informe') {
                                            if ($nombre == 'Primer Informe de Actividades') {
                                                $tiposParaMarcar = ['admin_primer_informe'];
                                            } else {
                                                $tiposParaMarcar = ['admin_segundo_informe'];
                                            }
                                        }
                                    @endphp
                                    
                                    <div class="bg-white rounded-lg border border-gray-200/80 hover:border-green-200 transition">
                                        <div class="grid grid-cols-1 md:grid-cols-12 gap-3 items-start md:items-center p-3">
                                            <!-- Nombre -->
                                            <div class="flex items-center gap-2 md:col-span-6">
                                                <span class="iconify w-5 h-5 text-red-500 flex-shrink-0" data-icon="mdi:file-pdf-box"></span>
                                                <span class="font-medium text-sm text-gray-800">{{ $nombre }}</span>
                                            </div>
                                            
                                            <!-- Estado -->
                                            <div class="flex items-center md:col-span-3">
                                                @if($config['tipo'] == 'informe')
                                                    @php
                                                        if ($nombre == 'Primer Informe de Actividades') {
                                                            $validado = $practica->reporte_parcial_validado ?? false;
                                                            $rechazado = $practica->reporte_parcial_rechazado ?? false;
                                                        } else {
                                                            $validado = $practica->reporte_final_validado ?? false;
                                                            $rechazado = $practica->reporte_final_rechazado ?? false;
                                                        }
                                                    @endphp
                                                    
                                                    @if($rechazado)
                                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-red-100 text-red-700 rounded-full text-xs font-medium">
                                                            <span class="iconify w-3.5 h-3.5" data-icon="mdi:close-circle"></span>
                                                            Rechazado
                                                        </span>
                                                    @elseif($validado)
                                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-green-100 text-green-700 rounded-full text-xs font-medium">
                                                            <span class="iconify w-3.5 h-3.5" data-icon="mdi:check-circle"></span>
                                                            Validado
                                                        </span>
                                                    @elseif($estaSubido)
                                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-yellow-100 text-yellow-700 rounded-full text-xs font-medium">
                                                            <span class="iconify w-3.5 h-3.5" data-icon="mdi:clock-outline"></span>
                                                            Pendiente validación
                                                        </span>
                                                    @else
                                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-gray-100 text-gray-500 rounded-full text-xs font-medium">
                                                            <span class="iconify w-3.5 h-3.5" data-icon="mdi:close-circle"></span>
                                                            No subido
                                                        </span>
                                                    @endif
                                                @else
                                                    {{-- Documentos administrativos --}}
                                                    @if($estaSubido)
                                                        @if($doc && $doc->estatus == 'validado')
                                                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-green-100 text-green-700 rounded-full text-xs font-medium">
                                                                <span class="iconify w-3.5 h-3.5" data-icon="mdi:check-circle"></span>
                                                                Validado
                                                            </span>
                                                        @elseif($doc && $doc->estatus == 'rechazado')
                                                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-red-100 text-red-700 rounded-full text-xs font-medium">
                                                                <span class="iconify w-3.5 h-3.5" data-icon="mdi:close-circle"></span>
                                                                Rechazado
                                                            </span>
                                                        @else
                                                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-yellow-100 text-yellow-700 rounded-full text-xs font-medium">
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
                                                @endif
                                            </div>
                                            
                                            <!-- COLUMNA DESCARGA -->
                                            <div class="flex flex-col sm:flex-row items-start sm:items-center gap-2 md:col-span-2 w-full md:w-auto">
                                                @if($nombre == 'Solicitud de Prácticas Profesionales' && $practica->fecha_inicio)
                                                    <a href="{{ route('practicas.word', $practica->id) }}" 
                                                       class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-green-600 hover:bg-green-700 text-white text-xs font-medium rounded-md transition w-full sm:w-auto justify-center">
                                                        <span class="iconify w-4 h-4" data-icon="mdi:download"></span>
                                                        <span>Descargar</span>
                                                    </a>
                                                @else
                                                    <span class="text-gray-300 text-xs w-full sm:w-auto text-center">—</span>
                                                @endif
                                            </div>
                                            
                                            <!-- COLUMNA ACCIONES + COMENTARIOS -->
                                            <div class="flex flex-wrap items-center justify-start sm:justify-end gap-1.5 md:col-span-1 w-full md:w-auto">
                                                @if($estaSubido)
                                                    @if(!$estaValidado)
                                                        {{-- Mostrar botones SOLO si NO está validado --}}
                                                        <a href="{{ route('practicas.' . $ruta, $practica->id) }}" 
                                                           class="inline-flex items-center gap-1 px-2.5 py-1.5 bg-blue-500 hover:bg-blue-600 text-white text-xs font-medium rounded-md transition whitespace-nowrap">
                                                            <span class="iconify w-3.5 h-3.5" data-icon="mdi:refresh"></span>
                                                            <span class="hidden sm:inline">Cambiar</span>
                                                        </a>
                                                        
                                                        @if($config['tipo'] == 'admin')
                                                            <button type="button" 
                                                                    onclick="mostrarModalEliminar('{{ route('practicas.eliminar-documento', [$practica->id, $nombre]) }}', 'documento')"
                                                                    class="inline-flex items-center gap-1 px-2.5 py-1.5 bg-red-500 hover:bg-red-600 text-white text-xs font-medium rounded-md transition whitespace-nowrap">
                                                                <span class="iconify w-3.5 h-3.5" data-icon="mdi:delete"></span>
                                                                <span class="hidden sm:inline">Eliminar</span>
                                                            </button>
                                                        @elseif($config['tipo'] == 'informe')
                                                            @php
                                                                $tipoInforme = ($nombre == 'Primer Informe de Actividades') ? 'primero' : 'segundo';
                                                            @endphp
                                                            <button type="button" 
                                                                    onclick="mostrarModalEliminar('{{ route('practicas.eliminar-informe', [$practica->id, $tipoInforme]) }}', 'informe')"
                                                                    class="inline-flex items-center gap-1 px-2.5 py-1.5 bg-red-500 hover:bg-red-600 text-white text-xs font-medium rounded-md transition whitespace-nowrap">
                                                                <span class="iconify w-3.5 h-3.5" data-icon="mdi:delete"></span>
                                                                <span class="hidden sm:inline">Eliminar</span>
                                                            </button>
                                                        @else
                                                            <button type="button" 
                                                                    onclick="mostrarModalEliminar('{{ route('practicas.eliminar-documento', [$practica->id, $nombre]) }}', 'elemento')"
                                                                    class="inline-flex items-center gap-1 px-2.5 py-1.5 bg-red-500 hover:bg-red-600 text-white text-xs font-medium rounded-md transition whitespace-nowrap">
                                                                <span class="iconify w-3.5 h-3.5" data-icon="mdi:delete"></span>
                                                                <span class="hidden sm:inline">Eliminar</span>
                                                            </button>
                                                        @endif
                                                    @else
                                                        {{-- Bloqueado (más compacto) --}}
                                                        <span class="inline-flex items-center gap-0.5 px-2 py-1 bg-gray-200 text-gray-500 text-[10px] sm:text-xs font-medium rounded-md cursor-not-allowed whitespace-nowrap">
                                                            <span class="iconify w-3 h-3 sm:w-3.5 sm:h-3.5" data-icon="mdi:lock"></span>
                                                            <span class="hidden xs:inline">Bloqueado</span>
                                                        </span>
                                                    @endif
                                                @else
                                                    <a href="{{ route('practicas.' . $ruta, $practica->id) }}" 
                                                       class="inline-flex items-center gap-1 px-2.5 py-1.5 bg-blue-600 hover:bg-blue-700 text-white text-xs font-medium rounded-md transition whitespace-nowrap">
                                                        <span class="iconify w-3.5 h-3.5" data-icon="mdi:cloud-upload"></span>
                                                        <span class="hidden sm:inline">Subir</span>
                                                    </a>
                                                @endif
                                                
                                                <!-- ========================================== -->
                                                <!-- BOTÓN DE COMENTARIOS CON HOVER -->
                                                <!-- ========================================== -->
                                                @if($tieneComentarios)
                                                    <div class="relative inline-block group" 
                                                         data-comentable-type="{{ $config['tipo'] == 'admin' ? 'App\\Models\\Documento' : 'App\\Models\\Practica' }}"
                                                         data-comentable-id="{{ $config['tipo'] == 'admin' ? ($doc->id ?? 0) : $practica->id }}"
                                                         data-tipos="{{ json_encode($tiposParaMarcar) }}">
                                                        <button type="button" 
                                                                class="comentario-btn inline-flex items-center justify-center w-8 h-8 rounded-full hover:bg-gray-200 transition relative"
                                                                onclick="marcarComentariosComoLeidos(this)">
                                                            <span class="iconify w-5 h-5 text-gray-600 group-hover:text-green-600 transition" 
                                                                  data-icon="mdi:comment-text-outline"></span>
                                                            
                                                            @if($comentariosNoLeidos > 0)
                                                                <span class="badge-notificacion absolute -top-0.5 -right-0.5 flex items-center justify-center w-4 h-4 bg-red-500 text-white text-[10px] font-bold rounded-full animate-pulse">
                                                                    {{ $comentariosNoLeidos }}
                                                                </span>
                                                            @endif
                                                        </button>
                                                        
                                                        <!-- Tooltip con comentarios -->
                                                        <div class="absolute right-0 mt-2 w-64 sm:w-80 bg-white rounded-lg shadow-2xl border border-gray-200 z-50 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200 pointer-events-none group-hover:pointer-events-auto md:right-0 md:left-auto">
                                                            <div class="absolute -top-2 right-4 w-3 h-3 bg-white border-l border-t border-gray-200 transform rotate-45 md:right-4"></div>
                                                            
                                                            <div class="p-3 max-h-48 overflow-y-auto">
                                                                <div class="flex items-center justify-between mb-2">
                                                                    <span class="text-xs font-semibold text-gray-700">Comentarios</span>
                                                                    <span class="text-[10px] text-gray-400">{{ $comentariosTooltip->count() }} comentario(s)</span>
                                                                </div>
                                                                
                                                                @foreach($comentariosTooltip->take(5) as $comentario)
                                                                    @php
                                                                        $esAdmin = str_contains($comentario->tipo, 'admin');
                                                                        $color = $esAdmin ? 'text-orange-600' : 'text-blue-600';
                                                                        $icono = $esAdmin ? 'mdi:account-check' : 'mdi:account';
                                                                        $nombreMostrar = $esAdmin ? 'Administrador' : 'Tú';
                                                                        $fondo = $esAdmin ? 'bg-orange-50' : 'bg-blue-50';
                                                                        
                                                                        $fechaTexto = 'Fecha no disponible';
                                                                        if ($comentario->created_at) {
                                                                            $fechaTexto = $comentario->created_at->diffForHumans();
                                                                        }
                                                                    @endphp
                                                                    <div class="text-xs {{ $color }} flex items-start gap-1.5 py-1.5 px-2 rounded-md {{ $fondo }} mb-1 last:mb-0">
                                                                        <span class="iconify w-3.5 h-3.5 mt-0.5 flex-shrink-0" data-icon="{{ $icono }}"></span>
                                                                        <span class="flex-1">
                                                                            <strong>{{ $nombreMostrar }}:</strong>
                                                                            {{ \Illuminate\Support\Str::limit($comentario->contenido, 60) }}
                                                                            <span class="text-gray-400 text-[10px] block">{{ $fechaTexto }}</span>
                                                                        </span>
                                                                    </div>
                                                                @endforeach
                                                                
                                                                @if($comentariosTooltip->count() > 5)
                                                                    <div class="text-center mt-1">
                                                                        <span class="text-[10px] text-gray-400">+ {{ $comentariosTooltip->count() - 5 }} más</span>
                                                                    </div>
                                                                @endif
                                                            </div>
                                                        </div>
                                                    </div>
                                                @else
                                                    <span class="inline-flex items-center justify-center w-8 h-8 rounded-full opacity-50">
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
        </div>
    @else
        <div class="bg-[#f8f8f8] rounded-xl shadow-md border border-gray-200/80 p-8 text-center">
            <span class="iconify w-16 h-16 text-gray-300 mx-auto mb-4" data-icon="mdi:file-document-outline"></span>
            <p class="text-gray-600 font-medium">No hay registro de Prácticas Profesionales para este usuario.</p>
            <p class="text-gray-400 text-sm mt-1">Completa el formulario de solicitud para comenzar.</p>
            <a href="{{ route('solicitud-practicas.create') }}" class="inline-flex items-center gap-2 mt-4 px-6 py-3 bg-green-700 hover:bg-green-800 text-white text-sm font-bold rounded-lg transition shadow-md hover:shadow-lg border border-green-800">
                <span class="iconify w-5 h-5" data-icon="mdi:plus-circle"></span>
                Solicitar Prácticas Profesionales
            </a>
        </div>
    @endif
</div>

<!-- ========================================== -->
<!-- MODAL DE CONFIRMACIÓN PARA ELIMINAR (FONDO TRANSPARENTE) -->
<!-- ========================================== -->
<div id="modalEliminar" class="hidden fixed inset-0 bg-black/30 backdrop-blur-[2px] flex items-center justify-center z-[9999] p-4">
    <div class="bg-white rounded-xl shadow-2xl border border-gray-200 max-w-md w-full p-6 sm:p-8 transform transition-all duration-300 scale-95 opacity-0" id="modalContent">
        <!-- Icono de advertencia -->
        <div class="flex justify-center mb-4">
            <div class="w-16 h-16 bg-red-50 rounded-full flex items-center justify-center">
                <span class="iconify w-10 h-10 text-red-500" data-icon="mdi:alert-circle"></span>
            </div>
        </div>
        
        <!-- Título -->
        <h3 class="text-xl font-bold text-gray-900 text-center mb-2">¿Estás seguro?</h3>
        
        <!-- Mensaje -->
        <div class="bg-gray-50 rounded-lg p-4 border border-gray-200 mb-4">
            <p class="text-sm text-gray-700 text-center" id="mensajeModalEliminar">
                Esta acción no se puede deshacer.
            </p>
        </div>
        
        <!-- Botones -->
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
function marcarComentariosComoLeidos(button) {
    const container = button.closest('.group');
    if (!container) return;
    
    const comentableType = container.dataset.comentableType;
    const comentableId = container.dataset.comentableId;
    const tipos = JSON.parse(container.dataset.tipos || '[]');
    
    if (!tipos.length || !comentableId) return;
    
    const data = {
        comentable_type: comentableType,
        comentable_id: comentableId,
        tipos: tipos,
        _token: '{{ csrf_token() }}'
    };
    
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
        if (data.success) {
            const badge = container.querySelector('.badge-notificacion');
            if (badge) {
                badge.style.display = 'none';
            }
        }
    })
    .catch(error => {
        console.error('Error al marcar comentarios como leídos:', error);
    });
}

document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.group').forEach(function(container) {
        container.addEventListener('mouseenter', function() {
            const button = container.querySelector('.comentario-btn');
            if (button) {
                const badge = container.querySelector('.badge-notificacion');
                if (badge && badge.style.display !== 'none') {
                    marcarComentariosComoLeidos(button);
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
</style>
@endsection