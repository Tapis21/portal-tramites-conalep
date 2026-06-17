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

    <!-- Título con icono -->
    <div class="flex items-center gap-3 mb-6">
        <span class="iconify w-8 h-8 sm:w-10 sm:h-10 text-green-700" data-icon="mdi:clipboard-text"></span>
        <h1 class="text-xl sm:text-2xl font-bold text-gray-800">Solicitud de Prácticas Profesionales</h1>
    </div>

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
    <form method="POST" action="{{ route('solicitud-practicas.store') }}" class="bg-[#f5f0e8] rounded-xl shadow-xl border border-[#d4c9b8] overflow-hidden" style="background: linear-gradient(145deg, #f5f0e8 0%, #faf6ef 100%);">
        @csrf

        <div class="p-4 sm:p-8 space-y-8">
            <!-- ========== 1. DATOS DEL ESTUDIANTE ========== -->
            <div class="border-b border-[#d4c9b8] pb-6">
                <h2 class="text-base sm:text-lg font-bold text-gray-800 mb-4 flex items-center gap-2">
                    <span class="iconify w-5 h-5 text-blue-600" data-icon="mdi:account"></span>
                    Datos del estudiante
                </h2>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                    <div class="bg-white/80 rounded-lg p-3 border border-[#d4c9b8]">
                        <label class="block text-xs text-gray-500 font-medium uppercase tracking-wider mb-1">Nombre completo</label>
                        <input type="text" value="{{ Auth::user()->name }} {{ Auth::user()->apellidos }}" 
                               class="w-full p-2 border-0 bg-transparent text-gray-800 text-sm font-medium focus:ring-0" readonly>
                    </div>
                    <div class="bg-white/80 rounded-lg p-3 border border-[#d4c9b8]">
                        <label class="block text-xs text-gray-500 font-medium uppercase tracking-wider mb-1">Matrícula</label>
                        <input type="text" value="{{ Auth::user()->matricula }}" 
                               class="w-full p-2 border-0 bg-transparent text-gray-800 text-sm font-medium focus:ring-0" readonly>
                    </div>
                    <div class="bg-white/80 rounded-lg p-3 border border-[#d4c9b8]">
                        <label class="block text-xs text-gray-500 font-medium uppercase tracking-wider mb-1">Carrera</label>
                        <input type="text" value="{{ Auth::user()->carrera }}" 
                               class="w-full p-2 border-0 bg-transparent text-gray-800 text-sm font-medium focus:ring-0" readonly>
                    </div>
                    <div class="bg-white/80 rounded-lg p-3 border border-[#d4c9b8]">
                        <label class="block text-xs text-gray-500 font-medium uppercase tracking-wider mb-1">Semestre</label>
                        <input type="text" value="{{ Auth::user()->semestre }}° Semestre" 
                               class="w-full p-2 border-0 bg-transparent text-gray-800 text-sm font-medium focus:ring-0" readonly>
                    </div>
                    <div class="bg-white/80 rounded-lg p-3 border border-[#d4c9b8]">
                        <label class="block text-xs text-gray-500 font-medium uppercase tracking-wider mb-1">Grupo</label>
                        <input type="text" value="{{ Auth::user()->grupo ?? 'No asignado' }}" 
                               class="w-full p-2 border-0 bg-transparent text-gray-800 text-sm font-medium focus:ring-0" readonly>
                    </div>
                    <div class="bg-white/80 rounded-lg p-3 border border-[#d4c9b8]">
                        <label class="block text-xs text-gray-500 font-medium uppercase tracking-wider mb-1">Turno</label>
                        <input type="text" value="{{ Auth::user()->nombre_turno }}" 
                               class="w-full p-2 border-0 bg-transparent text-gray-800 text-sm font-medium focus:ring-0" readonly>
                    </div>
                    <div class="bg-white/80 rounded-lg p-3 border border-[#d4c9b8] sm:col-span-2 lg:col-span-3">
                        <label class="block text-xs text-gray-500 font-medium uppercase tracking-wider mb-1">Generación</label>
                        <input type="text" value="{{ Auth::user()->nombre_periodo_actual }}" 
                               class="w-full p-2 border-0 bg-transparent text-gray-800 text-sm font-medium focus:ring-0" readonly>
                    </div>
                </div>
            </div>

            <!-- ========== 2. FECHAS Y HORARIO ========== -->
            <div class="border-b border-[#d4c9b8] pb-6">
                <h2 class="text-base sm:text-lg font-bold text-gray-800 mb-4 flex items-center gap-2">
                    <span class="iconify w-5 h-5 text-indigo-600" data-icon="mdi:calendar-clock"></span>
                    Fechas y horario de prácticas
                </h2>
                <div class="space-y-4">
                    @if(Auth::user()->turno)
                    <div class="bg-green-50/70 rounded-lg p-4 border border-green-200">
                        <label class="block text-gray-700 font-semibold text-sm mb-2">
                            <span class="iconify inline mr-1 align-middle text-amber-600" data-icon="mdi:clock-outline"></span>
                            Horario de prácticas
                        </label>
                        <select name="horario_id" id="horario_id" class="w-full p-2.5 border border-[#d4c9b8] rounded-lg bg-white/90 text-gray-700 text-sm focus:border-green-600 focus:ring-2 focus:ring-green-200 transition" required>
                            <option value="" disabled selected class="text-gray-400">— SELECCIONA UN HORARIO —</option>
                            @foreach($horarios as $horario)
                                <option value="{{ $horario->id }}" {{ old('horario_id') == $horario->id ? 'selected' : '' }}>
                                    {{ \Carbon\Carbon::parse($horario->hora_inicio)->format('g:i A') }} — 
                                    {{ \Carbon\Carbon::parse($horario->hora_fin)->format('g:i A') }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    @endif

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div class="bg-white/80 rounded-lg p-4 border border-[#d4c9b8]">
                            <label class="block text-gray-700 font-semibold text-sm mb-2">
                                <span class="iconify inline mr-1 align-middle text-emerald-600" data-icon="mdi:calendar-start"></span>
                                Fecha de inicio
                            </label>
                            <input type="date" name="fecha_inicio" id="fecha_inicio" value="{{ old('fecha_inicio') }}" 
                                   class="w-full p-2.5 border border-[#d4c9b8] rounded-lg bg-white/90 text-gray-700 text-sm focus:border-green-600 focus:ring-2 focus:ring-green-200 transition" required>
                            <p class="text-xs text-gray-500 mt-1.5 flex items-center gap-1">
                                <span class="iconify w-3.5 h-3.5 text-gray-400" data-icon="mdi:information-outline"></span>
                                No se permiten fines de semana
                            </p>
                        </div>
                        <div class="bg-white/80 rounded-lg p-4 border border-[#d4c9b8]">
                            <label class="block text-gray-700 font-semibold text-sm mb-2">
                                <span class="iconify inline mr-1 align-middle text-rose-600" data-icon="mdi:calendar-end"></span>
                                Fecha de finalización (aprox.)
                            </label>
                            <input type="text" id="fecha_finalizacion" 
                                   class="w-full p-2.5 border border-[#d4c9b8] rounded-lg bg-gray-100/70 text-gray-700 text-sm" 
                                   readonly placeholder="Selecciona fecha de inicio">
                            <p class="text-xs text-gray-500 mt-1.5 flex items-center gap-1">
                                <span class="iconify w-3.5 h-3.5 text-gray-400" data-icon="mdi:information-outline"></span>
                                Se suman 4 meses exactos
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ========== 3. DATOS DE LA INSTITUCIÓN O EMPRESA ========== -->
            <div class="border-b border-[#d4c9b8] pb-6">
                <h2 class="text-base sm:text-lg font-bold text-gray-800 mb-4 flex items-center gap-2">
                    <span class="iconify w-5 h-5 text-amber-700" data-icon="mdi:office-building"></span>
                    Datos de la institución o empresa
                </h2>
                <div class="space-y-4">
                    <div class="bg-green-50/70 rounded-lg p-4 border border-green-200">
                        <label class="block text-gray-700 font-semibold text-sm mb-2">
                            <span class="iconify inline mr-1 align-middle text-green-700" data-icon="mdi:domain"></span>
                            Institución / Empresa
                        </label>
                        <select name="empresa_id" class="w-full p-2.5 border border-[#d4c9b8] rounded-lg bg-white/90 text-gray-700 text-sm focus:border-green-600 focus:ring-2 focus:ring-green-200 transition" required>
                            <option value="" disabled selected class="text-gray-400">— SELECCIONA UNA EMPRESA —</option>
                            @foreach($empresas as $empresa)
                                <option value="{{ $empresa->id }}" {{ old('empresa_id') == $empresa->id ? 'selected' : '' }}>
                                    {{ $empresa->nombre }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div class="bg-white/80 rounded-lg p-4 border border-[#d4c9b8]">
                            <label class="block text-gray-700 font-semibold text-sm mb-2">
                                <span class="iconify inline mr-1 align-middle text-purple-600" data-icon="mdi:school"></span>
                                Grado académico (carta)
                            </label>
                            <select name="grado_academico_id" class="w-full p-2.5 border border-[#d4c9b8] rounded-lg bg-white/90 text-gray-700 text-sm focus:border-green-600 focus:ring-2 focus:ring-green-200 transition" required>
                                <option value="" disabled selected class="text-gray-400">— SELECCIONA UN GRADO —</option>
                                @foreach($grados as $grado)
                                    <option value="{{ $grado->id }}" {{ old('grado_academico_id') == $grado->id ? 'selected' : '' }}>
                                        {{ $grado->nombre }} ({{ $grado->abreviatura }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="bg-white/80 rounded-lg p-4 border border-[#d4c9b8]">
                            <label class="block text-gray-700 font-semibold text-sm mb-2">
                                <span class="iconify inline mr-1 align-middle text-cyan-600" data-icon="mdi:account-tie"></span>
                                Nombre de la persona
                            </label>
                            <input type="text" name="nombre_persona_carta" value="{{ old('nombre_persona_carta') }}" 
                                   class="w-full p-2.5 border border-[#d4c9b8] rounded-lg bg-white/90 text-gray-700 text-sm focus:border-green-600 focus:ring-2 focus:ring-green-200 transition" 
                                   placeholder="Ej: Lic. Juan Pérez" required>
                        </div>
                    </div>

                    <div class="bg-white/80 rounded-lg p-4 border border-[#d4c9b8]">
                        <label class="block text-gray-700 font-semibold text-sm mb-2">
                            <span class="iconify inline mr-1 align-middle text-orange-600" data-icon="mdi:badge-account"></span>
                            Cargo de la persona
                        </label>
                        <input type="text" name="cargo_persona_carta" value="{{ old('cargo_persona_carta') }}" 
                               class="w-full p-2.5 border border-[#d4c9b8] rounded-lg bg-white/90 text-gray-700 text-sm focus:border-green-600 focus:ring-2 focus:ring-green-200 transition" 
                               placeholder="Ej: Director de Recursos Humanos" required>
                    </div>

                    <div class="bg-white/80 rounded-lg p-4 border border-[#d4c9b8]">
                        <label class="block text-gray-700 font-semibold text-sm mb-2">
                            <span class="iconify inline mr-1 align-middle text-teal-600" data-icon="mdi:office-building-outline"></span>
                            Área asignada
                        </label>
                        <input type="text" name="area_asignada" value="{{ old('area_asignada') }}" 
                               class="w-full p-2.5 border border-[#d4c9b8] rounded-lg bg-white/90 text-gray-700 text-sm focus:border-green-600 focus:ring-2 focus:ring-green-200 transition" 
                               placeholder="Ej: Departamento de Sistemas" required>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div class="bg-white/80 rounded-lg p-4 border border-[#d4c9b8]">
                            <label class="block text-gray-700 font-semibold text-sm mb-2">
                                <span class="iconify inline mr-1 align-middle text-purple-400" data-icon="mdi:school-outline"></span>
                                Grado académico (jefe)
                            </label>
                            <select name="grado_academico_jefe_id" class="w-full p-2.5 border border-[#d4c9b8] rounded-lg bg-white/90 text-gray-700 text-sm focus:border-green-600 focus:ring-2 focus:ring-green-200 transition" required>
                                <option value="" disabled selected class="text-gray-400">— SELECCIONA UN GRADO —</option>
                                @foreach($grados as $grado)
                                    <option value="{{ $grado->id }}" {{ old('grado_academico_jefe_id') == $grado->id ? 'selected' : '' }}>
                                        {{ $grado->nombre }} ({{ $grado->abreviatura }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="bg-white/80 rounded-lg p-4 border border-[#d4c9b8]">
                            <label class="block text-gray-700 font-semibold text-sm mb-2">
                                <span class="iconify inline mr-1 align-middle text-sky-600" data-icon="mdi:account-tie-outline"></span>
                                Nombre del jefe inmediato
                            </label>
                            <input type="text" name="nombre_jefe_inmediato" value="{{ old('nombre_jefe_inmediato') }}" 
                                   class="w-full p-2.5 border border-[#d4c9b8] rounded-lg bg-white/90 text-gray-700 text-sm focus:border-green-600 focus:ring-2 focus:ring-green-200 transition" 
                                   placeholder="Ej: Ing. María González" required>
                        </div>
                    </div>

                    <div class="bg-white/80 rounded-lg p-4 border border-[#d4c9b8]">
                        <label class="block text-gray-700 font-semibold text-sm mb-2">
                            <span class="iconify inline mr-1 align-middle text-amber-600" data-icon="mdi:badge-account-outline"></span>
                            Cargo del jefe inmediato
                        </label>
                        <input type="text" name="cargo_jefe_inmediato" value="{{ old('cargo_jefe_inmediato') }}" 
                               class="w-full p-2.5 border border-[#d4c9b8] rounded-lg bg-white/90 text-gray-700 text-sm focus:border-green-600 focus:ring-2 focus:ring-green-200 transition" 
                               placeholder="Ej: Subdirector de Operaciones" required>
                    </div>

                    <div class="bg-white/80 rounded-lg p-4 border border-[#d4c9b8]">
                        <label class="block text-gray-700 font-semibold text-sm mb-2">
                            <span class="iconify inline mr-1 align-middle text-pink-600" data-icon="mdi:handshake"></span>
                            Apoyo al estudiante
                        </label>
                        <input type="text" name="apoyo_estudiante" value="{{ old('apoyo_estudiante') }}" 
                               class="w-full p-2.5 border border-[#d4c9b8] rounded-lg bg-white/90 text-gray-700 text-sm focus:border-green-600 focus:ring-2 focus:ring-green-200 transition" 
                               placeholder="Ej: Económico, equipo de cómputo">
                    </div>
                </div>
            </div>

            <!-- ========== BOTÓN DE ENVÍO ========== -->
            <div class="pt-4 flex flex-col sm:flex-row gap-3">
                <button type="submit" class="inline-flex items-center justify-center gap-2 px-8 py-3 bg-green-700 hover:bg-green-800 text-white text-sm font-bold rounded-lg transition shadow-md hover:shadow-lg border border-green-800">
                    <span class="iconify w-5 h-5" data-icon="mdi:send"></span>
                    Enviar solicitud
                </button>
                <a href="{{ route('dashboard') }}" class="inline-flex items-center justify-center gap-2 px-6 py-3 bg-[#d4c9b8] hover:bg-[#c4b9a8] text-gray-700 text-sm font-medium rounded-lg transition shadow-sm">
                    <span class="iconify w-5 h-5" data-icon="mdi:close"></span>
                    Cancelar
                </a>
            </div>
        </div>
    </form>
</div>

<!-- ========================================== -->
<!-- JAVASCRIPT PARA CÁLCULO DE FECHAS -->
<!-- ========================================== -->
<script>
    function ajustarFechaSiFinDeSemana(fecha) {
        var dia = fecha.getDay();
        if (dia === 6) {
            fecha.setDate(fecha.getDate() + 2);
        } else if (dia === 0) {
            fecha.setDate(fecha.getDate() + 1);
        }
        return fecha;
    }

    function formatearFecha(fecha) {
        var año = fecha.getFullYear();
        var mes = (fecha.getMonth() + 1).toString().padStart(2, '0');
        var dia = fecha.getDate().toString().padStart(2, '0');
        return `${año}-${mes}-${dia}`;
    }

    document.getElementById('fecha_inicio').addEventListener('change', function() {
        var fechaInicioStr = this.value;
        var finalizacionInput = document.getElementById('fecha_finalizacion');
        
        if (fechaInicioStr) {
            var partes = fechaInicioStr.split('-');
            var fecha = new Date(Date.UTC(partes[0], partes[1] - 1, partes[2]));
            
            // Para prácticas: 4 meses
            var mesDestino = fecha.getUTCMonth() + 4;
            var añoDestino = fecha.getUTCFullYear() + Math.floor(mesDestino / 12);
            var mesDestinoFinal = mesDestino % 12;
            
            var nuevaFecha = new Date(Date.UTC(añoDestino, mesDestinoFinal, fecha.getUTCDate()));
            
            if (nuevaFecha.getUTCMonth() !== mesDestinoFinal) {
                nuevaFecha.setUTCDate(0);
            }
            
            nuevaFecha = ajustarFechaSiFinDeSemana(nuevaFecha);
            finalizacionInput.value = formatearFecha(nuevaFecha);
        } else {
            finalizacionInput.value = '';
        }
    });
</script>
@endsection