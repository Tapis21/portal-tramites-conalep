@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto py-10">

    <!-- Botón Atrás (solo visible en móvil) -->
    <div class="sm:hidden mb-4 px-4">
        <a href="{{ route('dashboard') }}" class="inline-flex items-center gap-2 text-blue-600 hover:text-blue-800 transition">
            <span class="iconify w-5 h-5" data-icon="mdi:arrow-left"></span>
            Volver al inicio
        </a>
    </div>

    <h1 class="text-2xl font-bold mb-5">Mi Servicio Social</h1>

    @if(session('success'))
        <div class="bg-green-100 text-green-800 p-3 rounded mb-4">
            {{ session('success') }}
        </div>
    @endif

    @if($servicioSocial)
        <div class="bg-white p-6 rounded shadow">
            <!-- Fechas importantes -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
                <div class="border rounded-lg p-3">
                    <p class="text-xs text-gray-500">Fecha de inicio</p>
                    <p class="font-semibold">{{ $servicioSocial->fecha_inicio ? \Carbon\Carbon::parse($servicioSocial->fecha_inicio)->format('d/m/Y') : 'No definida' }}</p>
                </div>
                <div class="border rounded-lg p-3">
                    <p class="text-xs text-gray-500">Primer informe disponible</p>
                    <p class="font-semibold">{{ $servicioSocial->fecha_limite_primer_informe ? \Carbon\Carbon::parse($servicioSocial->fecha_limite_primer_informe)->format('d/m/Y') : 'No definida' }}</p>
                </div>
                <div class="border rounded-lg p-3">
                    <p class="text-xs text-gray-500">Segundo informe disponible</p>
                    <p class="font-semibold">{{ $servicioSocial->fecha_limite_segundo_informe ? \Carbon\Carbon::parse($servicioSocial->fecha_limite_segundo_informe)->format('d/m/Y') : 'No definida' }}</p>
                </div>
                <div class="border rounded-lg p-3">
                    <p class="text-xs text-gray-500">Fecha de finalización</p>
                    <p class="font-semibold">{{ $servicioSocial->fecha_limite_segundo_informe ? \Carbon\Carbon::parse($servicioSocial->fecha_limite_segundo_informe)->format('d/m/Y') : 'No definida' }}</p>
                </div>
            </div>

            <p>
                <strong>Estatus:</strong>
                <span class="px-3 py-1 text-sm rounded-full inline-flex items-center gap-1
                    @if($servicioSocial->estatus == 'liberado') bg-green-100 text-green-800
                    @elseif($servicioSocial->estatus == 'pendiente_revision') bg-yellow-100 text-yellow-800
                    @elseif($servicioSocial->estatus == 'en_progreso') bg-blue-100 text-blue-800
                    @elseif($servicioSocial->estatus == 'pendiente') bg-yellow-100 text-yellow-800
                    @else bg-gray-100 text-gray-800 @endif">
                    
                    @if($servicioSocial->estatus == 'liberado')
                        <span class="iconify w-4 h-4" data-icon="mdi:check-decagram"></span>
                        Trámite liberado
                    @elseif($servicioSocial->estatus == 'pendiente_revision')
                        <span class="iconify w-4 h-4" data-icon="mdi:clock-check"></span>
                        Pendiente de revisión
                    @elseif($servicioSocial->estatus == 'en_progreso')
                        <span class="iconify w-4 h-4" data-icon="mdi:progress-clock"></span>
                        En progreso
                    @elseif($servicioSocial->estatus == 'pendiente')
                        <span class="iconify w-4 h-4" data-icon="mdi:clock-outline"></span>
                        Pendiente (documentación incompleta)
                    @else
                        <span class="iconify w-4 h-4" data-icon="mdi:file-document-outline"></span>
                        No solicitado
                    @endif
                </span>
            </p>

            <!-- Documentos del Servicio Social -->
            <div class="mt-6">
                <h3 class="font-semibold text-gray-700 mb-4">Documentos del Servicio Social</h3>
                
                @php
                    $documentosOrdenados = [
                        'Solicitud de Servicio Social' => ['tipo' => 'admin', 'ruta' => 'subir-solicitud', 'campo' => null],
                        'Elección de Modalidad' => ['tipo' => 'admin', 'ruta' => 'subir-modalidad', 'campo' => null],
                        'Carta de Presentación de Servicio Social' => ['tipo' => 'admin', 'ruta' => 'subir-carta-presentacion', 'campo' => null],
                        'Carta de Aceptación' => ['tipo' => 'admin', 'ruta' => 'subir-carta-aceptacion', 'campo' => null],
                        'Primer Informe de Actividades Trimestral' => ['tipo' => 'informe', 'ruta' => 'subir-reporte-parcial', 'campo' => 'reporte_parcial_subido'],
                        'Segundo Informe de Actividades Trimestral' => ['tipo' => 'informe', 'ruta' => 'subir-reporte-final', 'campo' => 'reporte_final_subido'],
                        'Evaluación de Competencias del Desempeño' => ['tipo' => 'admin', 'ruta' => 'subir-evaluacion', 'campo' => null],
                        'Carta de Liberación de Servicio Social' => ['tipo' => 'admin', 'ruta' => 'subir-liberacion', 'campo' => null],
                    ];
                    
                    $subidos = \App\Models\Documento::where('user_id', Auth::id())
                        ->whereHas('tipoDocumento', function($q) use ($documentosOrdenados) {
                            $q->whereIn('nombre', array_keys($documentosOrdenados))
                              ->where('tramite', 'SS');
                        })
                        ->where('activo', true)
                        ->with('tipoDocumento')
                        ->get()
                        ->pluck('tipoDocumento.nombre')
                        ->toArray();
                @endphp
                
                <div class="overflow-x-auto">
                    <div class="md:min-w-full">
                        <!-- Encabezados - AHORA CON 4 COLUMNAS -->
                        <div class="hidden md:grid md:grid-cols-12 gap-4 px-3 py-2 bg-gray-100 rounded-t-lg text-xs font-semibold text-gray-600 mb-2">
                            <div class="col-span-5">Documento</div>
                            <div class="col-span-3">Estado</div>
                            <div class="col-span-2">Descarga</div>
                            <div class="col-span-2">Acciones</div>
                        </div>
                        
                        <div class="space-y-2">
                            @foreach($documentosOrdenados as $nombre => $config)
                                @php
                                    $ruta = $config['ruta'];
                                    $estaSubido = false;
                                    
                                    if ($config['tipo'] == 'admin') {
                                        $docActual = \App\Models\Documento::where('user_id', Auth::id())
                                            ->whereHas('tipoDocumento', function($q) use ($nombre) {
                                                $q->where('nombre', $nombre)
                                                  ->where('tramite', 'SS');
                                            })->first();
                                        
                                        $estaSubido = $docActual && $docActual->archivo_pdf !== null;
                                        $doc = $docActual;
                                    } else {
                                        $campo = $config['campo'];
                                        $estaSubido = $servicioSocial->$campo ?? false;
                                        $doc = null;
                                    }
                                @endphp
                                
                                <div class="bg-gray-50 rounded-lg">
                                    <div class="grid grid-cols-1 md:grid-cols-12 gap-3 items-center p-3">
                                        <!-- Nombre -->
                                        <div class="flex items-center gap-2 md:col-span-5">
                                            <span class="iconify w-5 h-5 text-gray-500 flex-shrink-0" data-icon="mdi:file-pdf-box"></span>
                                            <span class="font-medium text-sm">{{ $nombre }}</span>
                                        </div>
                                        
                                        <!-- Estado -->
                                        <div class="flex items-center md:col-span-3">
                                            @if($config['tipo'] == 'informe')
                                                @php
                                                    if ($nombre == 'Primer Informe de Actividades Trimestral') {
                                                        $validado = $servicioSocial->reporte_parcial_validado ?? false;
                                                    } else {
                                                        $validado = $servicioSocial->reporte_final_validado ?? false;
                                                    }
                                                @endphp
                                                
                                                @if($validado)
                                                    <span class="flex items-center gap-1 text-green-600 text-sm">
                                                        <span class="iconify w-4 h-4" data-icon="mdi:check-decagram"></span>
                                                        Validado
                                                    </span>
                                                @elseif($estaSubido)
                                                    <span class="flex items-center gap-1 text-yellow-600 text-sm">
                                                        <span class="iconify w-4 h-4" data-icon="mdi:clock-outline"></span>
                                                        Pendiente de validación
                                                    </span>
                                                @else
                                                    <span class="flex items-center gap-1 text-yellow-600 text-sm">
                                                        <span class="iconify w-4 h-4" data-icon="mdi:clock-outline"></span>
                                                        No subido
                                                    </span>
                                                @endif
                                            @else
                                                {{-- Documentos administrativos --}}
                                                @if($estaSubido)
                                                    @if($doc && $doc->estatus == 'validado')
                                                        <span class="flex items-center gap-1 text-green-600 text-sm">
                                                            <span class="iconify w-4 h-4" data-icon="mdi:check-decagram"></span>
                                                            Validado
                                                        </span>
                                                    @elseif($doc && $doc->estatus == 'rechazado')
                                                        <span class="flex items-center gap-1 text-red-600 text-sm">
                                                            <span class="iconify w-4 h-4" data-icon="mdi:close-circle"></span>
                                                            Rechazado
                                                        </span>
                                                    @else
                                                        <span class="flex items-center gap-1 text-yellow-600 text-sm">
                                                            <span class="iconify w-4 h-4" data-icon="mdi:clock-outline"></span>
                                                            Pendiente
                                                        </span>
                                                    @endif
                                                @else
                                                    <span class="flex items-center gap-1 text-yellow-600 text-sm">
                                                        <span class="iconify w-4 h-4" data-icon="mdi:clock-outline"></span>
                                                        No subido
                                                    </span>
                                                @endif
                                            @endif
                                        </div>
                                        
                                        <!-- COLUMNA DESCARGA - SOLO PARA SOLICITUD -->
                                        <div class="md:col-span-2">
                                            @if($nombre == 'Solicitud de Servicio Social' && $servicioSocial->fecha_inicio)
                                                <a href="{{ route('servicio-social.word', $servicioSocial->id) }}" 
                                                class="inline-flex items-center px-3 py-1.5 bg-purple-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-purple-700 transition w-full justify-center">
                                                    <span class="iconify mr-1 w-4 h-4" data-icon="mdi:download"></span>
                                                    Descargar
                                                </a>
                                            @else
                                                <span class="text-gray-400 text-xs">—</span>
                                            @endif
                                        </div>
                                        
                                        <!-- COLUMNA ACCIONES (Subir/Cambiar/Eliminar) -->
                                        <div class="flex flex-wrap items-center gap-2 md:col-span-2">
                                            @if($estaSubido)
                                                <a href="{{ route('servicio-social.' . $ruta, $servicioSocial->id) }}" 
                                                class="inline-flex items-center px-3 py-1.5 bg-blue-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-600 transition">
                                                    <span class="iconify mr-1 w-4 h-4" data-icon="mdi:refresh"></span>
                                                    Cambiar
                                                </a>
                                                
                                                <!-- Eliminar -->
                                                <form method="POST" action="{{ route('servicio-social.eliminar-documento', [$servicioSocial->id, $nombre]) }}" class="inline" 
                                                    onsubmit="return confirm('¿Estás seguro de eliminar este documento?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="inline-flex items-center px-3 py-1.5 bg-red-500 text-white text-xs rounded-md hover:bg-red-600">
                                                        <span class="iconify mr-1 w-4 h-4" data-icon="mdi:delete"></span>
                                                        Eliminar
                                                    </button>
                                                </form>
                                            @else
                                                <a href="{{ route('servicio-social.' . $ruta, $servicioSocial->id) }}" 
                                                class="inline-flex items-center px-3 py-1.5 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 transition">
                                                    <span class="iconify mr-1 w-4 h-4" data-icon="mdi:cloud-upload"></span>
                                                    Subir
                                                </a>
                                            @endif
                                        </div>
                                    </div>

                                    <!-- Comentarios para documentos administrativos -->
                                    @if($config['tipo'] == 'admin')
                                        @if($doc && $doc->comentarios && $doc->comentarios->count() > 0)
                                            <div class="px-3 pb-3 pt-1 border-t border-gray-200 mt-2 space-y-2">
                                                @foreach($doc->comentarios as $comentario)
                                                    <div class="text-xs {{ $comentario->tipo == 'admin' ? 'text-orange-600' : 'text-blue-600' }} flex items-start gap-1">
                                                        <span class="iconify w-3 h-3 mt-0.5 flex-shrink-0" 
                                                            data-icon="{{ $comentario->tipo == 'admin' ? 'mdi:account-check' : 'mdi:account' }}"></span>
                                                        <span>
                                                            <strong>{{ $comentario->tipo == 'admin' ? 'Administrador' : 'Tú' }}:</strong>
                                                            {{ $comentario->contenido }}
                                                            <span class="text-gray-400 text-xs ml-1">({{ $comentario->created_at->diffForHumans() }})</span>
                                                        </span>
                                                    </div>
                                                @endforeach
                                            </div>
                                        @endif
                                    @endif

                                    <!-- Comentarios para informes -->
                                    @if($config['tipo'] == 'informe')
                                        @php
                                            if ($nombre == 'Primer Informe de Actividades Trimestral') {
                                                $tiposComentarios = ['estudiante_primer_informe', 'admin_primer_informe'];
                                            } elseif ($nombre == 'Segundo Informe de Actividades Trimestral') {
                                                $tiposComentarios = ['estudiante_segundo_informe', 'admin_segundo_informe'];
                                            } else {
                                                $tiposComentarios = [];
                                            }
                                            
                                            $informeComentarios = \App\Models\Comentario::where('comentable_type', 'App\Models\ServicioSocial')
                                                ->where('comentable_id', $servicioSocial->id)
                                                ->whereIn('tipo', $tiposComentarios)
                                                ->orderBy('created_at', 'asc')
                                                ->get();
                                        @endphp

                                        @if($informeComentarios->count() > 0)
                                            <div class="px-3 pb-3 pt-1 border-t border-gray-200 mt-2 space-y-2">
                                                @foreach($informeComentarios as $comentario)
                                                    @php
                                                        $esAdmin = str_contains($comentario->tipo, 'admin');
                                                        $color = $esAdmin ? 'text-orange-600' : 'text-blue-600';
                                                        $icono = $esAdmin ? 'mdi:account-check' : 'mdi:account';
                                                        $nombreMostrar = $esAdmin ? 'Administrador' : 'Tú';
                                                    @endphp
                                                    <div class="text-xs {{ $color }} flex items-start gap-1">
                                                        <span class="iconify w-3 h-3 mt-0.5 flex-shrink-0" data-icon="{{ $icono }}"></span>
                                                        <span>
                                                            <strong>{{ $nombreMostrar }}:</strong>
                                                            {{ $comentario->contenido }}
                                                            <span class="text-gray-400 text-xs ml-1">({{ $comentario->created_at->diffForHumans() }})</span>
                                                        </span>
                                                    </div>
                                                @endforeach
                                            </div>
                                        @endif
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @else
        <p>No hay registro de Servicio Social para este usuario.</p>
    @endif
</div>
@endsection