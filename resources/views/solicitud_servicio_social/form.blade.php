@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto px-3 sm:px-6 py-4 sm:py-10">
    <h1 class="text-xl sm:text-2xl font-bold mb-5">Solicitud de Servicio Social</h1>

    @if ($errors->any())
        <div class="bg-red-100 text-red-800 p-3 rounded mb-4">
            <ul class="text-sm">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('solicitud-servicio-social.store') }}" class="bg-white p-4 sm:p-6 rounded shadow">
        @csrf

        <!-- ========== 1. DATOS DEL ESTUDIANTE ========== -->
        <div class="border-b pb-4 mb-4">
            <h2 class="text-base sm:text-lg font-bold text-gray-800 mb-3">Datos del estudiante</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-3 sm:gap-4">
                <div>
                    <label class="block text-gray-700 font-semibold text-sm sm:text-base mb-1">Nombre completo</label>
                    <input type="text" value="{{ Auth::user()->name }} {{ Auth::user()->apellidos }}" class="w-full p-1.5 sm:p-2 border rounded bg-gray-100 text-sm" readonly>
                </div>
                <div>
                    <label class="block text-gray-700 font-semibold text-sm sm:text-base mb-1">Matrícula</label>
                    <input type="text" value="{{ Auth::user()->matricula }}" class="w-full p-1.5 sm:p-2 border rounded bg-gray-100 text-sm" readonly>
                </div>
                <div>
                    <label class="block text-gray-700 font-semibold text-sm sm:text-base mb-1">Carrera</label>
                    <input type="text" value="{{ Auth::user()->carrera }}" class="w-full p-1.5 sm:p-2 border rounded bg-gray-100 text-sm" readonly>
                </div>
                <div>
                    <label class="block text-gray-700 font-semibold text-sm sm:text-base mb-1">Semestre</label>
                    <input type="text" value="{{ Auth::user()->semestre }}° Semestre" class="w-full p-1.5 sm:p-2 border rounded bg-gray-100 text-sm" readonly>
                </div>
                <div>
                    <label class="block text-gray-700 font-semibold text-sm sm:text-base mb-1">Grupo</label>
                    <input type="text" value="{{ Auth::user()->grupo ?? 'No asignado' }}" class="w-full p-1.5 sm:p-2 border rounded bg-gray-100 text-sm" readonly>
                </div>
                <div>
                    <label class="block text-gray-700 font-semibold text-sm sm:text-base mb-1">Turno</label>
                    <input type="text" value="{{ Auth::user()->nombre_turno }}" class="w-full p-1.5 sm:p-2 border rounded bg-gray-100 text-sm" readonly>
                </div>
                <div>
                    <label class="block text-gray-700 font-semibold text-sm sm:text-base mb-1">Generación</label>
                    <input type="text" value="{{ Auth::user()->nombre_periodo_actual }}" class="w-full p-1.5 sm:p-2 border rounded bg-gray-100 text-sm" readonly>
                </div>
            </div>
        </div>

        <!-- ========== 2. FECHAS Y HORARIO ========== -->
        <div class="border-b pb-4 mb-4">
            <h2 class="text-base sm:text-lg font-bold text-gray-800 mb-3">Fechas y horario del servicio</h2>

            @if(Auth::user()->turno)
            <div class="mb-4">
                <label class="block text-gray-700 font-semibold text-sm sm:text-base mb-2">Horario de servicio</label>
                <select name="horario_id" id="horario_id" class="w-full p-1.5 sm:p-2 border rounded bg-white text-gray-700 text-sm" required>
                    <option value="" disabled selected class="text-gray-400">-- SELECCIONA UN HORARIO --</option>
                    @foreach($horarios as $horario)
                        <option value="{{ $horario->id }}" {{ old('horario_id') == $horario->id ? 'selected' : '' }}>
                            {{ \Carbon\Carbon::parse($horario->hora_inicio)->format('g:i A') }} - 
                            {{ \Carbon\Carbon::parse($horario->hora_fin)->format('g:i A') }}
                        </option>
                    @endforeach
                </select>
            </div>
            @endif

            <div class="grid grid-cols-1 md:grid-cols-2 gap-3 sm:gap-4 mb-4">
                <div>
                    <label class="block text-gray-700 font-semibold text-sm sm:text-base mb-2">Fecha de inicio</label>
                    <input type="date" name="fecha_inicio" id="fecha_inicio" value="{{ old('fecha_inicio') }}" class="w-full p-1.5 sm:p-2 border rounded text-sm" required>
                    <p class="text-xs text-gray-500 mt-1">No se permiten fines de semana</p>
                </div>
                <div>
                    <label class="block text-gray-700 font-semibold text-sm sm:text-base mb-2">Fecha de finalización</label>
                    <input type="text" id="fecha_finalizacion" class="w-full p-1.5 sm:p-2 border rounded bg-gray-100 text-sm" readonly placeholder="Selecciona fecha inicio">
                    <p class="text-xs text-gray-500 mt-1">Se suman 6 meses exactos</p>
                </div>
            </div>
        </div>

        <!-- ========== 3. DATOS DE LA INSTITUCION O EMPRESA ========== -->
        <div class="border-b pb-4 mb-4">
            <h2 class="text-base sm:text-lg font-bold text-gray-800 mb-3">Datos de la institución o empresa</h2>
            
            <div class="mb-4">
                <label class="block text-gray-700 font-semibold text-sm sm:text-base mb-2">Institución / Empresa</label>
                <select name="empresa_id" class="w-full p-1.5 sm:p-2 border rounded bg-white text-gray-700 text-sm" required>
                    <option value="" disabled selected class="text-gray-400">-- SELECCIONA UNA EMPRESA --</option>
                    @foreach($empresas as $empresa)
                        <option value="{{ $empresa->id }}" {{ old('empresa_id') == $empresa->id ? 'selected' : '' }}>
                            {{ $empresa->nombre }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-3 sm:gap-4 mb-4">
                <div>
                    <label class="block text-gray-700 font-semibold text-sm sm:text-base mb-2">Grado académico (carta)</label>
                    <select name="grado_academico_id" class="w-full p-1.5 sm:p-2 border rounded bg-white text-gray-700 text-sm" required>
                        <option value="" disabled selected class="text-gray-400">-- SELECCIONA UN GRADO --</option>
                        @foreach($grados as $grado)
                            <option value="{{ $grado->id }}" {{ old('grado_academico_id') == $grado->id ? 'selected' : '' }}>
                                {{ $grado->nombre }} ({{ $grado->abreviatura }})
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-gray-700 font-semibold text-sm sm:text-base mb-2">Nombre de la persona</label>
                    <input type="text" name="nombre_persona_carta" value="{{ old('nombre_persona_carta') }}" class="w-full p-1.5 sm:p-2 border rounded text-sm" required>
                </div>
            </div>

            <div class="mb-4">
                <label class="block text-gray-700 font-semibold text-sm sm:text-base mb-2">Cargo de la persona</label>
                <input type="text" name="cargo_persona_carta" value="{{ old('cargo_persona_carta') }}" class="w-full p-1.5 sm:p-2 border rounded text-sm" placeholder="Ej: Director de RH" required>
            </div>

            <div class="mb-4">
                <label class="block text-gray-700 font-semibold text-sm sm:text-base mb-2">Área asignada</label>
                <input type="text" name="area_asignada" value="{{ old('area_asignada') }}" class="w-full p-1.5 sm:p-2 border rounded text-sm" required>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-3 sm:gap-4 mb-4">
                <div>
                    <label class="block text-gray-700 font-semibold text-sm sm:text-base mb-2">Grado académico (jefe)</label>
                    <select name="grado_academico_jefe_id" class="w-full p-1.5 sm:p-2 border rounded bg-white text-gray-700 text-sm" required>
                        <option value="" disabled selected class="text-gray-400">-- SELECCIONA UN GRADO --</option>
                        @foreach($grados as $grado)
                            <option value="{{ $grado->id }}" {{ old('grado_academico_jefe_id') == $grado->id ? 'selected' : '' }}>
                                {{ $grado->nombre }} ({{ $grado->abreviatura }})
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-gray-700 font-semibold text-sm sm:text-base mb-2">Nombre del jefe inmediato</label>
                    <input type="text" name="nombre_jefe_inmediato" value="{{ old('nombre_jefe_inmediato') }}" class="w-full p-1.5 sm:p-2 border rounded text-sm" required>
                </div>
            </div>

            <div class="mb-4">
                <label class="block text-gray-700 font-semibold text-sm sm:text-base mb-2">Cargo del jefe inmediato</label>
                <input type="text" name="cargo_jefe_inmediato" value="{{ old('cargo_jefe_inmediato') }}" class="w-full p-1.5 sm:p-2 border rounded text-sm" placeholder="Ej: Subdirector" required>
            </div>

            <div class="mb-4">
                <label class="block text-gray-700 font-semibold text-sm sm:text-base mb-2">Apoyo al estudiante</label>
                <input type="text" name="apoyo_estudiante" value="{{ old('apoyo_estudiante') }}" class="w-full p-1.5 sm:p-2 border rounded text-sm" placeholder="Ej: Económico, equipo de cómputo">
            </div>
        </div>

        <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 w-full sm:w-auto text-sm sm:text-base">
            Enviar solicitud
        </button>
    </form>
</div>

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
            
            var mesDestino = fecha.getUTCMonth() + 6;
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