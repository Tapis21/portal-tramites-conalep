@extends('layouts.app')

@section('content')
<div class="max-w-3xl mx-auto px-4 sm:px-6 py-6 sm:py-10">

    <!-- Botón Atrás (solo visible en móvil) -->
    <div class="sm:hidden mb-4">
        <a href="{{ route('servicio-social.index') }}" class="inline-flex items-center gap-2 text-green-700 hover:text-green-900 transition text-sm font-medium">
            <span class="iconify w-5 h-5" data-icon="mdi:arrow-left-circle"></span>
            Volver al inicio
        </a>
    </div>

    <!-- Título con icono - NARANJA/ÁMBAR -->
    <div class="flex items-center gap-3 mb-6">
        <span class="iconify w-8 h-8 sm:w-10 sm:h-10 text-amber-600" data-icon="mdi:file-clock"></span>
        <h1 class="text-xl sm:text-2xl font-bold text-gray-800">Subir Segundo Informe de Actividades Trimestral</h1>
    </div>

    <!-- ========================================== -->
    <!-- ALERTA DE PRÓRROGA (si aplica) -->
    <!-- ========================================== -->
    @if(session('warning'))
        <div class="bg-amber-50 border-l-4 border-amber-500 text-amber-700 p-4 rounded-md mb-4 flex items-start gap-3 shadow-sm" role="alert">
            <span class="iconify w-5 h-5 flex-shrink-0 mt-0.5 text-amber-500" data-icon="mdi:alert"></span>
            <div class="flex-1">
                <p class="text-sm font-medium">{{ session('warning') }}</p>
            </div>
            <button type="button" class="text-amber-500 hover:text-amber-700 transition cursor-pointer" onclick="this.parentElement.remove()">
                <span class="iconify w-5 h-5" data-icon="mdi:close"></span>
            </button>
        </div>
    @endif

    <!-- ========================================== -->
    <!-- ALERTA INFORMATIVA (si aplica - más de 5 días antes) -->
    <!-- ========================================== -->
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

    <!-- ========================================== -->
    <!-- ALERTAS DE ERRORES MEJORADAS -->
    <!-- ========================================== -->
    @if ($errors->any())
        <div class="bg-red-50 border-l-4 border-red-500 text-red-700 p-4 rounded-md mb-4 flex items-start gap-3 shadow-sm" role="alert">
            <span class="iconify w-5 h-5 flex-shrink-0 mt-0.5 text-red-500" data-icon="mdi:alert-circle"></span>
            <div class="flex-1">
                <p class="text-sm font-medium mb-1">Por favor, corrige los siguientes errores:</p>
                <ul class="text-xs list-disc list-inside space-y-0.5">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            <button type="button" class="text-red-500 hover:text-red-700 transition cursor-pointer" onclick="this.parentElement.remove()">
                <span class="iconify w-5 h-5" data-icon="mdi:close"></span>
            </button>
        </div>
    @endif

    <!-- ========================================== -->
    <!-- FORMULARIO - ESTILO HOJA / CREMA -->
    <!-- ========================================== -->
    <form method="POST" action="{{ route('servicio-social.guardar-reporte-final', $servicioSocial->id) }}" enctype="multipart/form-data" 
          class="bg-[#f5f0e8] rounded-xl shadow-xl border border-[#d4c9b8] overflow-hidden" style="background: linear-gradient(145deg, #f5f0e8 0%, #faf6ef 100%);"
          id="uploadForm">
        @csrf

        <div class="p-4 sm:p-8 space-y-6">
            <!-- ========== INFORMACIÓN DE FECHAS ========== -->
            <div class="bg-white/70 rounded-lg p-4 border {{ $estaVencido ? 'border-red-300' : 'border-[#d4c9b8]' }}">
                <div class="flex items-start gap-3">
                    @if($estaVencido)
                        <span class="iconify w-5 h-5 text-red-600 flex-shrink-0 mt-0.5" data-icon="mdi:alert-circle"></span>
                    @else
                        <span class="iconify w-5 h-5 text-amber-600 flex-shrink-0 mt-0.5" data-icon="mdi:calendar-clock"></span>
                    @endif
                    <div>
                        @if($estaVencido)
                            <p class="text-sm text-red-700 font-semibold">
                                El plazo para subir este informe venció el {{ $fechaFormateada }}
                            </p>
                            <p class="text-xs text-red-500 mt-1">
                                <span class="iconify w-3.5 h-3.5 inline align-middle mr-0.5" data-icon="mdi:information-outline"></span>
                                Aunque el plazo ya venció, aún puedes subir el informe. Será marcado como <strong>"Subido en destiempo"</strong>.
                            </p>
                        @else
                            <p class="text-sm text-gray-700">
                                <span class="font-semibold">Fecha límite para subir:</span>
                                {{ $fechaFormateada }}
                            </p>
                            @php
                                $fechaLimiteCarbon = \Carbon\Carbon::parse($fechaLimite);
                                $fechaInicioSubida = $fechaLimiteCarbon->copy()->subDays(5);
                                $fechaFinSubida = $fechaLimiteCarbon->copy()->addDays(5);
                            @endphp
                            <p class="text-xs text-gray-500 mt-1">
                                <span class="iconify w-3.5 h-3.5 inline align-middle mr-0.5" data-icon="mdi:information-outline"></span>
                                Puedes subir este informe desde el <strong>{{ $fechaInicioSubida->format('d/m/Y') }}</strong> 
                                hasta el <strong>{{ $fechaFinSubida->format('d/m/Y') }}</strong>
                            </p>
                            <p class="text-xs text-gray-400 mt-0.5">
                                <span class="iconify w-3.5 h-3.5 inline align-middle mr-0.5" data-icon="mdi:clock-outline"></span>
                                Se permite subir 5 días antes y hasta 5 días después de la fecha límite.
                            </p>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Si está vencido, mostrar advertencia adicional -->
            @if($estaVencido)
                <div class="bg-red-50/80 rounded-lg p-3 border border-red-200">
                    <p class="text-xs text-red-600 flex items-center gap-2">
                        <span class="iconify w-4 h-4 flex-shrink-0" data-icon="mdi:clock-alert"></span>
                        <span>Este informe se está subiendo fuera del plazo establecido.</span>
                    </p>
                </div>
            @endif

            <!-- ========== INFORMACIÓN DEL TRÁMITE ========== -->
            <div class="bg-white/70 rounded-lg p-4 border border-[#d4c9b8]">
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 text-sm">
                    <div>
                        <span class="font-semibold text-gray-600">Estudiante:</span>
                        <span class="text-gray-800 block">{{ Auth::user()->name }} {{ Auth::user()->apellidos }}</span>
                    </div>
                    <div>
                        <span class="font-semibold text-gray-600">Matrícula:</span>
                        <span class="text-gray-800 block">{{ Auth::user()->matricula }}</span>
                    </div>
                    <div>
                        <span class="font-semibold text-gray-600">Carrera:</span>
                        <span class="text-gray-800 block">{{ Auth::user()->carrera }}</span>
                    </div>
                    <div>
                        <span class="font-semibold text-gray-600">Horas requeridas:</span>
                        <span class="text-amber-600 font-medium block">480 horas (total)</span>
                    </div>
                </div>
            </div>

            <!-- ========== INFORMACIÓN DEL PROGRESO (CORREGIDO) ========== -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div class="bg-white/70 rounded-lg p-4 border border-[#d4c9b8]">
                    <div class="flex items-center gap-2">
                        <span class="iconify w-5 h-5 text-blue-600" data-icon="mdi:clock-outline"></span>
                        <span class="text-sm text-gray-600 font-medium">Horas completadas</span>
                    </div>
                    <p class="text-xl font-bold text-gray-800 mt-1">
                        {{ $horasCompletadas }} / 480
                        @if($horasCompletadas >= 480)
                            <span class="text-xs text-green-600 font-normal ml-2">
                                <span class="iconify w-3.5 h-3.5 inline align-middle mr-0.5" data-icon="mdi:check-circle"></span>
                                Completado
                            </span>
                        @else
                            <span class="text-xs text-amber-600 font-normal ml-2">
                                <span class="iconify w-3.5 h-3.5 inline align-middle mr-0.5" data-icon="mdi:clock-outline"></span>
                                Faltan {{ 480 - $horasCompletadas }} horas
                            </span>
                        @endif
                    </p>
                </div>
                <div class="bg-white/70 rounded-lg p-4 border border-[#d4c9b8]">
                    <div class="flex items-center gap-2">
                        <span class="iconify w-5 h-5 text-amber-600" data-icon="mdi:information-outline"></span>
                        <span class="text-sm text-gray-600 font-medium">Requisito mínimo</span>
                    </div>
                    <p class="text-xl font-bold text-amber-600 mt-1">480 horas</p>
                </div>
            </div>

            <!-- ========== CAMPO DE ARCHIVO ========== -->
            <div class="bg-white/80 rounded-lg p-4 border border-[#d4c9b8]">
                <label class="block text-gray-700 font-semibold text-sm mb-2">
                    <span class="iconify inline mr-1 align-middle text-red-600" data-icon="mdi:file-pdf-box"></span>
                    Reporte final (PDF) <span class="text-red-500">*</span>
                </label>
                <div class="relative">
                    <input type="file" name="reporte_pdf" accept=".pdf" required 
                           class="w-full p-2.5 border border-[#d4c9b8] rounded-lg bg-white/90 text-gray-700 text-sm focus:border-green-600 focus:ring-2 focus:ring-green-200 transition file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-green-50 file:text-green-700 hover:file:bg-green-100 cursor-pointer"
                           id="archivo_pdf">
                </div>
                <p class="text-xs text-gray-500 mt-1.5 flex items-center gap-1">
                    <span class="iconify w-3.5 h-3.5 text-gray-400" data-icon="mdi:information-outline"></span>
                    Máximo 5MB. Solo archivos PDF.
                </p>
                <!-- Indicador de tamaño del archivo -->
                <div id="tamaño-archivo" class="hidden mt-2 text-xs font-medium flex items-center gap-1.5"></div>
            </div>

            <!-- ========== CAMPO DE COMENTARIO ========== -->
            <div class="bg-white/80 rounded-lg p-4 border border-[#d4c9b8]">
                <label class="block text-gray-700 font-semibold text-sm mb-2">
                    <span class="iconify inline mr-1 align-middle text-amber-600" data-icon="mdi:comment-text-outline"></span>
                    Comentario <span class="text-gray-400 font-normal">(opcional)</span>
                </label>
                <textarea name="comentario" rows="3" 
                          class="w-full p-2.5 border border-[#d4c9b8] rounded-lg bg-white/90 text-gray-700 text-sm focus:border-green-600 focus:ring-2 focus:ring-green-200 transition resize-y"
                          placeholder="Ej: Este es mi segundo informe, revisar por favor..."></textarea>
                <p class="text-xs text-gray-500 mt-1.5 flex items-center gap-1">
                    <span class="iconify w-3.5 h-3.5 text-gray-400" data-icon="mdi:information-outline"></span>
                    Puedes dejar un comentario para el administrador.
                </p>
            </div>

            <!-- ========== BOTONES DE ACCIÓN ========== -->
            <div class="pt-4 flex flex-col sm:flex-row gap-3">
                <button type="submit" class="inline-flex items-center justify-center gap-2 px-8 py-3 bg-green-700 hover:bg-green-800 text-white text-sm font-bold rounded-lg transition shadow-md hover:shadow-lg border border-green-800">
                    <span class="iconify w-5 h-5" data-icon="mdi:cloud-upload"></span>
                    Subir reporte final
                </button>
                <a href="{{ route('servicio-social.index') }}" class="inline-flex items-center justify-center gap-2 px-6 py-3 bg-[#d4c9b8] hover:bg-[#c4b9a8] text-gray-700 text-sm font-medium rounded-lg transition shadow-sm">
                    <span class="iconify w-5 h-5" data-icon="mdi:close"></span>
                    Cancelar
                </a>
            </div>
        </div>
    </form>
</div>

<!-- ========================================== -->
<!-- ALERTA PERSONALIZADA (MODAL) -->
<!-- ========================================== -->
<div id="alertaPersonalizada" class="hidden fixed inset-0 bg-black/50 backdrop-blur-sm flex items-center justify-center z-[9999] p-4">
    <div class="bg-[#f5f0e8] rounded-xl shadow-2xl border border-[#d4c9b8] max-w-md w-full p-6 sm:p-8" style="background: linear-gradient(145deg, #f5f0e8 0%, #faf6ef 100%);">
        <div class="flex justify-center mb-4">
            <div class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center">
                <span class="iconify w-10 h-10 text-red-600" data-icon="mdi:alert-circle"></span>
            </div>
        </div>
        
        <h3 class="text-xl font-bold text-gray-800 text-center mb-2">¡Archivo demasiado grande!</h3>
        
        <div class="bg-white/70 rounded-lg p-4 border border-[#d4c9b8] mb-4">
            <p class="text-sm text-gray-700 text-center">
                El archivo que intentas subir excede el límite permitido.
            </p>
            <div class="flex items-center justify-center gap-4 mt-3 text-xs">
                <span class="flex items-center gap-1 text-green-700">
                    <span class="iconify w-4 h-4" data-icon="mdi:check-circle"></span>
                    Máximo: <strong>5 MB</strong>
                </span>
                <span class="flex items-center gap-1 text-red-600">
                    <span class="iconify w-4 h-4" data-icon="mdi:close-circle"></span>
                    Tu archivo: <strong id="tamañoArchivoAlerta">0 MB</strong>
                </span>
            </div>
        </div>
        
        <div class="bg-amber-50/80 rounded-lg p-3 border border-amber-200 mb-5">
            <p class="text-xs text-amber-700 flex items-center gap-2">
                <span class="iconify w-4 h-4 flex-shrink-0" data-icon="mdi:lightbulb-outline"></span>
                <span>Comprime el PDF o usa un archivo más pequeño.</span>
            </p>
        </div>
        
        <button onclick="cerrarAlerta()" class="w-full py-2.5 bg-green-700 hover:bg-green-800 text-white font-semibold rounded-lg transition shadow-md hover:shadow-lg border border-green-800">
            Entendido
        </button>
    </div>
</div>

<!-- ========================================== -->
<!-- JAVASCRIPT PARA VALIDACIÓN DE TAMAÑO -->
<!-- ========================================== -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const inputFile = document.getElementById('archivo_pdf');
        const form = document.getElementById('uploadForm');
        const tamañoDiv = document.getElementById('tamaño-archivo');
        const MAX_SIZE = 5 * 1024 * 1024; // 5 MB en bytes

        inputFile.addEventListener('change', function() {
            const archivo = this.files[0];
            if (archivo) {
                const tamañoMB = (archivo.size / (1024 * 1024)).toFixed(2);
                const esValido = archivo.size <= MAX_SIZE;
                
                const icono = esValido ? 'mdi:check-circle' : 'mdi:alert-circle';
                const color = esValido ? 'text-green-600' : 'text-red-600';
                const mensaje = esValido ? 'Dentro del límite' : 'Excede el límite de 5 MB';
                
                tamañoDiv.className = 'mt-2 text-xs font-medium ' + color + ' flex items-center gap-1.5';
                tamañoDiv.innerHTML = `
                    <span class="iconify w-4 h-4" data-icon="${icono}"></span>
                    Tamaño del archivo: ${tamañoMB} MB (${mensaje})
                `;
                tamañoDiv.classList.remove('hidden');

                if (!esValido) {
                    mostrarAlertaPersonalizada(tamañoMB);
                }
            } else {
                tamañoDiv.classList.add('hidden');
            }
        });

        form.addEventListener('submit', function(e) {
            const archivo = inputFile.files[0];
            if (archivo && archivo.size > MAX_SIZE) {
                e.preventDefault();
                const tamañoMB = (archivo.size / (1024 * 1024)).toFixed(2);
                mostrarAlertaPersonalizada(tamañoMB);
            }
        });
    });

    function mostrarAlertaPersonalizada(tamañoMB) {
        const alerta = document.getElementById('alertaPersonalizada');
        const tamañoSpan = document.getElementById('tamañoArchivoAlerta');
        tamañoSpan.textContent = tamañoMB + ' MB';
        alerta.classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }

    function cerrarAlerta() {
        const alerta = document.getElementById('alertaPersonalizada');
        alerta.classList.add('hidden');
        document.body.style.overflow = 'auto';
    }

    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            cerrarAlerta();
        }
    });

    document.getElementById('alertaPersonalizada').addEventListener('click', function(e) {
        if (e.target === this) {
            cerrarAlerta();
        }
    });
</script>
@endsection