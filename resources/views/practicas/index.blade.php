@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 py-6 sm:py-10">

    <!-- Botón Atrás (solo visible en móvil) -->
    <div class="sm:hidden mb-4">
        <a href="{{ route('dashboard') }}" class="inline-flex items-center gap-2 text-blue-600 hover:text-blue-800 transition text-sm">
            <span class="iconify w-4 h-4" data-icon="mdi:arrow-left"></span>
            Volver al inicio
        </a>
    </div>

    <h1 class="text-xl sm:text-2xl font-bold mb-5">Mis Prácticas Profesionales</h1>

    @if(session('success'))
        <div class="bg-green-100 text-green-800 p-3 rounded mb-4 text-sm">
            {{ session('success') }}
        </div>
    @endif

    @if($practica)
        <div class="bg-white p-4 sm:p-6 rounded shadow">
            
            <!-- Botón descargar solicitud (Word) -->
            <div class="flex justify-end mb-4">
                <a href="{{ route('practicas.word', $practica->id) }}" 
                   class="inline-flex items-center gap-2 bg-green-600 text-white px-3 py-1.5 sm:px-4 sm:py-2 rounded text-sm hover:bg-green-700 transition">
                    <span class="iconify w-4 h-4" data-icon="mdi:file-word"></span>
                    Descargar solicitud (Word)
                </a>
            </div>

            <!-- Fechas importantes -->
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 mb-6">
                <div class="border rounded-lg p-2 sm:p-3">
                    <p class="text-xs text-gray-500">Inicio</p>
                    <p class="text-sm sm:text-base font-semibold">{{ $practica->fecha_inicio ? \Carbon\Carbon::parse($practica->fecha_inicio)->format('d/m/Y') : 'No definida' }}</p>
                </div>
                <div class="border rounded-lg p-2 sm:p-3">
                    <p class="text-xs text-gray-500">1er Informe</p>
                    <p class="text-sm sm:text-base font-semibold">{{ $practica->fecha_limite_parcial ? \Carbon\Carbon::parse($practica->fecha_limite_parcial)->format('d/m/Y') : 'No definida' }}</p>
                </div>
                <div class="border rounded-lg p-2 sm:p-3">
                    <p class="text-xs text-gray-500">2do Informe</p>
                    <p class="text-sm sm:text-base font-semibold">{{ $practica->fecha_limite_final ? \Carbon\Carbon::parse($practica->fecha_limite_final)->format('d/m/Y') : 'No definida' }}</p>
                </div>
                <div class="border rounded-lg p-2 sm:p-3">
                    <p class="text-xs text-gray-500">Finalización</p>
                    <p class="text-sm sm:text-base font-semibold">{{ $practica->fecha_limite_final ? \Carbon\Carbon::parse($practica->fecha_limite_final)->format('d/m/Y') : 'No definida' }}</p>
                </div>
            </div>

            <p class="mb-4">
                <strong class="text-sm sm:text-base">Estatus:</strong>
                <span class="px-2 sm:px-3 py-1 text-xs sm:text-sm rounded-full inline-flex items-center gap-1
                    @if($practica->estatus == 'liberado') bg-green-100 text-green-800
                    @elseif($practica->estatus == 'pendiente_revision') bg-yellow-100 text-yellow-800
                    @elseif($practica->estatus == 'en_progreso') bg-blue-100 text-blue-800
                    @elseif($practica->estatus == 'pendiente') bg-yellow-100 text-yellow-800
                    @else bg-gray-100 text-gray-800 @endif">
                    
                    @if($practica->estatus == 'liberado')
                        <span class="iconify w-3 h-3 sm:w-4 sm:h-4" data-icon="mdi:check-decagram"></span>
                        Trámite liberado
                    @elseif($practica->estatus == 'pendiente_revision')
                        <span class="iconify w-3 h-3 sm:w-4 sm:h-4" data-icon="mdi:clock-check"></span>
                        Pendiente de revisión
                    @elseif($practica->estatus == 'en_progreso')
                        <span class="iconify w-3 h-3 sm:w-4 sm:h-4" data-icon="mdi:progress-clock"></span>
                        En progreso
                    @elseif($practica->estatus == 'pendiente')
                        <span class="iconify w-3 h-3 sm:w-4 sm:h-4" data-icon="mdi:clock-outline"></span>
                        Pendiente (documentación incompleta)
                    @else
                        <span class="iconify w-3 h-3 sm:w-4 sm:h-4" data-icon="mdi:file-document-outline"></span>
                        No solicitado
                    @endif
                </span>
            </p>

            <!-- Documentos de Prácticas Profesionales -->
            <div class="mt-6">
                <h3 class="font-semibold text-gray-700 text-sm sm:text-base mb-4">Documentos de Prácticas Profesionales</h3>
                
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
                        <!-- Encabezados desktop -->
                        <div class="hidden md:grid md:grid-cols-12 gap-4 px-3 py-2 bg-gray-100 rounded-t-lg text-xs font-semibold text-gray-600 mb-2">
                            <div class="col-span-6">Documento</div>
                            <div class="col-span-3">Estado</div>
                            <div class="col-span-3">Acciones</div>
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
                                                  ->where('tramite', 'PP');
                                            })->first();
                                        
                                        $estaSubido = $docActual && $docActual->archivo_pdf !== null;
                                        $doc = $docActual;
                                    } else {
                                        $campo = $config['campo'];
                                        $estaSubido = $practica->$campo ?? false;
                                        $doc = null;
                                    }
                                @endphp
                                
                                <div class="bg-gray-50 rounded-lg">
                                    <div class="grid grid-cols-1 md:grid-cols-12 gap-3 items-center p-3">
                                        <!-- Nombre -->
                                        <div class="flex items-center gap-2 md:col-span-6">
                                            <span class="iconify w-5 h-5 text-gray-500 flex-shrink-0" data-icon="mdi:file-pdf-box"></span>
                                            <span class="font-medium text-sm">{{ $nombre }}</span>
                                        </div>
                                        
                                        <!-- Estado -->
                                        <div class="flex items-center md:col-span-3">
                                            @if($config['tipo'] == 'informe')
                                                @php
                                                    if ($nombre == 'Primer Informe de Actividades') {
                                                        $validado = $practica->reporte_parcial_validado ?? false;
                                                    } else {
                                                        $validado = $practica->reporte_final_validado ?? false;
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
                                                        Pendiente validación
                                                    </span>
                                                @else
                                                    <span class="flex items-center gap-1 text-yellow-600 text-sm">
                                                        <span class="iconify w-4 h-4" data-icon="mdi:clock-outline"></span>
                                                        No subido
                                                    </span>
                                                @endif
                                            @else
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
                                        
                                        <!-- Acciones -->
                                        <div class="flex flex-wrap items-center gap-2 md:col-span-3">
                                            @if($estaSubido)
                                                <a href="{{ route('practicas.' . $ruta, $practica->id) }}" 
                                                   class="inline-flex items-center px-2 py-1 sm:px-3 sm:py-1.5 bg-blue-500 rounded-md font-semibold text-xs text-white hover:bg-blue-600 transition">
                                                    <span class="iconify mr-1 w-3 h-3" data-icon="mdi:refresh"></span>
                                                    Cambiar
                                                </a>
                                                
                                                @if($config['tipo'] == 'admin')
                                                    <form method="POST" action="{{ route('practicas.eliminar-documento', [$practica->id, $nombre]) }}" class="inline" 
                                                        onsubmit="return confirm('¿Estás seguro de eliminar este documento?')">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="inline-flex items-center px-2 py-1 sm:px-3 sm:py-1.5 bg-red-500 text-white text-xs rounded-md hover:bg-red-600">
                                                            <span class="iconify mr-1 w-3 h-3" data-icon="mdi:delete"></span>
                                                            Eliminar
                                                        </button>
                                                    </form>
                                                @else
                                                    @php
                                                        $tipoInforme = ($nombre == 'Primer Informe de Actividades') ? 'primero' : 'segundo';
                                                    @endphp
                                                    <form method="POST" action="{{ route('practicas.eliminar-informe', [$practica->id, $tipoInforme]) }}" class="inline"
                                                        onsubmit="return confirm('¿Estás seguro de eliminar este informe?')">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="inline-flex items-center px-2 py-1 sm:px-3 sm:py-1.5 bg-red-500 text-white text-xs rounded-md hover:bg-red-600">
                                                            <span class="iconify mr-1 w-3 h-3" data-icon="mdi:delete"></span>
                                                            Eliminar
                                                        </button>
                                                    </form>
                                                @endif
                                            @else
                                                <a href="{{ route('practicas.' . $ruta, $practica->id) }}" 
                                                   class="inline-flex items-center px-2 py-1 sm:px-3 sm:py-1.5 bg-blue-600 rounded-md font-semibold text-xs text-white hover:bg-blue-700 transition">
                                                    <span class="iconify mr-1 w-3 h-3" data-icon="mdi:cloud-upload"></span>
                                                    Subir
                                                </a>
                                            @endif
                                        </div>
                                    </div>

                                    <!-- Comentarios -->
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
                                                            <span class="text-gray-400 ml-1">({{ $comentario->created_at->diffForHumans() }})</span>
                                                        </span>
                                                    </div>
                                                @endforeach
                                            </div>
                                        @endif
                                    @endif

                                    @if($config['tipo'] == 'informe')
                                        @php
                                            $tiposComentarios = $nombre == 'Primer Informe de Actividades' 
                                                ? ['estudiante_primer_informe', 'admin_primer_informe']
                                                : ['estudiante_segundo_informe', 'admin_segundo_informe'];
                                            
                                            $informeComentarios = \App\Models\Comentario::where('comentable_type', 'App\Models\Practica')
                                                ->where('comentable_id', $practica->id)
                                                ->whereIn('tipo', $tiposComentarios)
                                                ->orderBy('created_at', 'asc')
                                                ->get();
                                        @endphp

                                        @if($informeComentarios->count() > 0)
                                            <div class="px-3 pb-3 pt-1 border-t border-gray-200 mt-2 space-y-2">
                                                @foreach($informeComentarios as $comentario)
                                                    @php
                                                        $esAdmin = str_contains($comentario->tipo, 'admin');
                                                    @endphp
                                                    <div class="text-xs {{ $esAdmin ? 'text-orange-600' : 'text-blue-600' }} flex items-start gap-1">
                                                        <span class="iconify w-3 h-3 mt-0.5 flex-shrink-0" data-icon="{{ $esAdmin ? 'mdi:account-check' : 'mdi:account' }}"></span>
                                                        <span>
                                                            <strong>{{ $esAdmin ? 'Administrador' : 'Tú' }}:</strong>
                                                            {{ $comentario->contenido }}
                                                            <span class="text-gray-400 ml-1">({{ $comentario->created_at->diffForHumans() }})</span>
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
        <div class="bg-white p-6 rounded shadow text-center">
            <p class="text-gray-500">No hay registro de Prácticas Profesionales para este usuario.</p>
            <a href="{{ route('solicitud-practicas.create') }}" class="inline-block mt-4 bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                Solicitar Prácticas Profesionales
            </a>
        </div>
    @endif
</div>
@endsection