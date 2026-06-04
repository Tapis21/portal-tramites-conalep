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
                <span class="px-3 py-1 text-sm rounded-full 
                    @if($servicioSocial->estatus == 'Liberado') bg-green-100 text-green-800
                    @elseif($servicioSocial->estatus == 'pendiente_revision') bg-yellow-100 text-yellow-800
                    @elseif($servicioSocial->estatus == 'en_progreso') bg-blue-100 text-blue-800
                    @else bg-gray-100 text-gray-800 @endif">
                    @if($servicioSocial->estatus == 'pendiente_revision')
                        Pendiente de revisión por administrador
                    @else
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
                <div class="space-y-3 text-sm">
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

                    @foreach($documentosOrdenados as $nombre => $config)
                        @php
                            $estaSubido = false;
                            $ruta = $config['ruta'];
                            
                            if ($config['tipo'] == 'admin') {
                                $estaSubido = in_array($nombre, $subidos);
                            } else {
                                // Para informes (vienen de servicio_social)
                                $campo = $config['campo'];
                                $estaSubido = $servicioSocial->$campo ?? false;
                            }
                        @endphp
                        
                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                            <div class="flex items-center gap-2">
                                <span class="iconify w-5 h-5 text-gray-500" data-icon="mdi:file-pdf-box"></span>
                                <span class="font-medium">{{ $nombre }}</span>
                            </div>
                            
                            <div class="flex items-center gap-3">
                                <!-- Estado -->
                                @if($estaSubido)
                                    <span class="flex items-center gap-1 text-green-600">
                                        <span class="iconify w-5 h-5" data-icon="mdi:check-circle"></span>
                                        Subido
                                    </span>
                                @else
                                    <span class="flex items-center gap-1 text-yellow-600">
                                        <span class="iconify w-5 h-5" data-icon="mdi:clock-outline"></span>
                                        Pendiente
                                    </span>
                                @endif
                                
                                <!-- Botón de acción -->
                                @if($estaSubido)
                                    <!-- Si ya está subido, mostrar "Reemplazar" -->
                                    <a href="{{ route('servicio-social.' . $ruta, $servicioSocial->id) }}" 
                                    class="inline-flex items-center px-3 py-1 bg-gray-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-600 transition">
                                        <span class="iconify mr-1 w-4 h-4" data-icon="mdi:refresh"></span>
                                        Cambiar
                                    </a>
                                @else
                                    <!-- Si no está subido, mostrar "Subir" -->
                                    <a href="{{ route('servicio-social.' . $ruta, $servicioSocial->id) }}" 
                                    class="inline-flex items-center px-3 py-1 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 transition">
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
    @else
        <p>No hay registro de Servicio Social para este usuario.</p>
    @endif
</div>
@endsection
