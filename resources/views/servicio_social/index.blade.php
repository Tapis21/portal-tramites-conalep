@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto py-10">
    <h1 class="text-2xl font-bold mb-5">Mi Servicio Social</h1>

    @if(session('success'))
        <div class="bg-green-100 text-green-800 p-3 rounded mb-4">
            {{ session('success') }}
        </div>
    @endif

    @if($servicioSocial)
        <div class="bg-white p-6 rounded shadow">
            <p><strong>Horas requeridas:</strong> {{ $servicioSocial->horas_requeridas }}</p>
            <p><strong>Horas completadas:</strong> {{ $servicioSocial->horas_completadas }}</p>
            <p>
                <strong>Estatus:</strong>
                <span class="px-3 py-1 text-sm rounded-full inline-flex items-center gap-1
                    @if($servicioSocial->estatus == 'liberado') bg-green-100 text-green-800
                    @elseif($servicioSocial->estatus == 'pendiente_revision') bg-yellow-100 text-yellow-800
                    @elseif($servicioSocial->estatus == 'en_progreso') bg-blue-100 text-blue-800
                    @else bg-gray-100 text-gray-800 @endif">
                    
                    @if($servicioSocial->estatus == 'pendiente_revision')
                        <span class="iconify w-4 h-4" data-icon="mdi:clock-outline"></span>
                        Pendiente de revisión
                    @elseif($servicioSocial->estatus == 'liberado')
                        <span class="iconify w-4 h-4" data-icon="mdi:check-decagram"></span>
                        Trámite liberado
                    @elseif($servicioSocial->estatus == 'en_progreso')
                        <span class="iconify w-4 h-4" data-icon="mdi:progress-clock"></span>
                        En progreso
                    @else
                        <span class="iconify w-4 h-4" data-icon="mdi:file-document-outline"></span>
                        {{ ucfirst($servicioSocial->estatus) }}
                    @endif
                </span>
            </p>

            <!-- Definir documentos subidos para usar en botones -->
            @php
                $subidos = \App\Models\Documento::where('user_id', Auth::id())
                    ->whereHas('tipoDocumento', function($q) {
                        $q->whereIn('nombre', [
                            'Solicitud de Servicio Social',
                            'Elección de Modalidad',
                            'Carta de Presentación de Servicio Social',
                            'Carta de Aceptación',
                            'Evaluación de Competencias del Desempeño',
                            'Carta de Liberación de Servicio Social'
                        ]);
                    })
                    ->with('tipoDocumento')
                    ->get()
                    ->pluck('tipoDocumento.nombre')
                    ->toArray();
            @endphp

            <!-- Botones de acción -->
            <div class="mt-6 flex flex-wrap gap-4">
                <!-- Actualizar horas (solo admin o pruebas) -->
                <a href="{{ route('servicio-social.edit', $servicioSocial->id) }}" 
                class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 transition">
                    <span class="iconify mr-2 w-5 h-5" data-icon="mdi:pencil"></span>
                    Actualizar horas
                </a>
            </div>

            <!-- Documentos del Servicio Social (orden correcto) -->
            <div class="mt-6">
                <h3 class="font-semibold text-gray-700 mb-4">Documentos del Servicio Social</h3>
                
                @php
                    // Definir los documentos en el orden correcto
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
                    
                    // Obtener documentos administrativos subidos
                    $subidos = \App\Models\Documento::where('user_id', Auth::id())
                        ->whereHas('tipoDocumento', function($q) use ($documentosOrdenados) {
                            $q->whereIn('nombre', array_keys($documentosOrdenados));
                        })
                        ->with('tipoDocumento')
                        ->get()
                        ->pluck('tipoDocumento.nombre')
                        ->toArray();
                @endphp
                
                <div class="overflow-x-auto">
                    <div class="md:min-w-full">
                        <!-- Encabezados (solo visible en desktop) -->
                        <div class="hidden md:grid md:grid-cols-12 gap-4 px-3 py-2 bg-gray-100 rounded-t-lg text-xs font-semibold text-gray-600 mb-2">
                            <div class="col-span-6">Documento</div>
                            <div class="col-span-3">Estado</div>
                            <div class="col-span-3">Acciones</div>
                        </div>
                        
                        <div class="space-y-2">
                            @foreach($documentosOrdenados as $nombre => $config)
                                @php
                                    $estaSubido = false;
                                    $ruta = $config['ruta'];
                                    
                                    if ($config['tipo'] == 'admin') {
                                        $estaSubido = in_array($nombre, $subidos);
                                    } else {
                                        $campo = $config['campo'];
                                        $estaSubido = $servicioSocial->$campo ?? false;
                                    }
                                @endphp
                                
                                <div class="grid grid-cols-1 md:grid-cols-12 gap-3 items-center p-3 bg-gray-50 rounded-lg">
                                    <!-- Nombre del documento -->
                                    <div class="flex items-center gap-2 md:col-span-6">
                                        <span class="iconify w-5 h-5 text-gray-500 flex-shrink-0" data-icon="mdi:file-document-outline"></span>
                                        <span class="font-medium text-sm">{{ $nombre }}</span>
                                    </div>
                                    
                                    <!-- Estado -->
                                    <div class="flex items-center md:col-span-3">
                                        @if($estaSubido)
                                            <span class="flex items-center gap-1 text-green-600 text-sm">
                                                <span class="iconify w-4 h-4" data-icon="mdi:check-circle"></span>
                                                Subido
                                            </span>
                                        @else
                                            <span class="flex items-center gap-1 text-yellow-600 text-sm">
                                                <span class="iconify w-4 h-4" data-icon="mdi:clock-outline"></span>
                                                Pendiente
                                            </span>
                                        @endif
                                    </div>
                                    
                                    <!-- Acciones -->
                                    <div class="flex flex-wrap items-center gap-2 md:col-span-3">
                                        @if($estaSubido)
                                            <!-- Reemplazar -->
                                            <a href="{{ route('servicio-social.' . $ruta, $servicioSocial->id) }}" 
                                            class="inline-flex items-center px-3 py-1.5 bg-blue-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-600 transition">
                                                <span class="iconify mr-1 w-4 h-4" data-icon="mdi:refresh"></span>
                                                Reemplazar
                                            </a>
                                            
                                            <!-- Eliminar -->
                                            <form method="POST" action="{{ route('servicio-social.eliminar-documento', [$servicioSocial->id, $nombre]) }}" class="inline" 
                                                onsubmit="return confirm('¿Estás seguro de eliminar este documento? Esta acción no se puede deshacer.')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" 
                                                        class="inline-flex items-center px-3 py-1.5 bg-red-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-600 transition">
                                                    <span class="iconify mr-1 w-4 h-4" data-icon="mdi:delete"></span>
                                                    Eliminar
                                                </button>
                                            </form>
                                        @else
                                            <!-- Subir -->
                                            <a href="{{ route('servicio-social.' . $ruta, $servicioSocial->id) }}" 
                                            class="inline-flex items-center px-3 py-1.5 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 transition">
                                                <span class="iconify mr-1 w-4 h-4" data-icon="mdi:cloud-upload"></span>
                                                Subir
                                            </a>
                                        @endif
                                    </div>
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
