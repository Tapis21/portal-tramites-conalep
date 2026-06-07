@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto py-10">
    <h1 class="text-2xl font-bold mb-5">Solicitud de Servicio Social</h1>

    @if ($errors->any())
        <div class="bg-red-100 text-red-800 p-3 rounded mb-4">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('solicitud-servicio-social.store') }}" class="bg-white p-6 rounded shadow">
        @csrf

        <!-- Empresa (catálogo) -->
        <div class="mb-4">
            <label class="block text-gray-700 font-semibold mb-2">Institución / Empresa</label>
            <select name="empresa_id" class="w-full p-2 border rounded" required>
                <option value="">Selecciona una empresa</option>
                @foreach($empresas as $empresa)
                    <option value="{{ $empresa->id }}" {{ old('empresa_id') == $empresa->id ? 'selected' : '' }}>
                        {{ $empresa->nombre }}
                    </option>
                @endforeach
            </select>
        </div>

        <!-- Grado académico (catálogo) -->
        <div class="mb-4">
            <label class="block text-gray-700 font-semibold mb-2">Grado académico de quien va dirigida la carta</label>
            <select name="grado_academico_id" class="w-full p-2 border rounded" required>
                <option value="">Selecciona un grado</option>
                @foreach($grados as $grado)
                    <option value="{{ $grado->id }}" {{ old('grado_academico_id') == $grado->id ? 'selected' : '' }}>
                        {{ $grado->nombre }} ({{ $grado->abreviatura }})
                    </option>
                @endforeach
            </select>
        </div>

        <!-- Nombre de la persona (texto libre) -->
        <div class="mb-4">
            <label class="block text-gray-700 font-semibold mb-2">Nombre de la persona a quien va dirigida</label>
            <input type="text" name="nombre_persona_carta" value="{{ old('nombre_persona_carta') }}" class="w-full p-2 border rounded" required>
        </div>

        <!-- Área asignada (texto libre) -->
        <div class="mb-4">
            <label class="block text-gray-700 font-semibold mb-2">Área asignada</label>
            <input type="text" name="area_asignada" value="{{ old('area_asignada') }}" class="w-full p-2 border rounded" required>
        </div>

        <!-- Apoyo al estudiante (texto libre) -->
        <div class="mb-4">
            <label class="block text-gray-700 font-semibold mb-2">Apoyo al estudiante</label>
            <input type="text" name="apoyo_estudiante" value="{{ old('apoyo_estudiante') }}" class="w-full p-2 border rounded" placeholder="Ej: Económico, equipo de cómputo, ninguno">
        </div>

        <!-- Fecha de inicio -->
        <div class="mb-4">
            <label class="block text-gray-700 font-semibold mb-2">Fecha de inicio</label>
            <input type="text" 
                   name="fecha_inicio" 
                   id="fecha_inicio"
                   value="{{ old('fecha_inicio') }}" 
                   class="w-full p-2 border rounded" 
                   required
                   placeholder="Selecciona una fecha">
            <p class="text-xs text-gray-500 mt-1">No se permiten fines de semana (sábado o domingo).</p>
        </div>

        <!-- Fecha de finalización (solo lectura, se calcula automáticamente) -->
        <div class="mb-4">
            <label class="block text-gray-700 font-semibold mb-2">Fecha de finalización (aprox.)</label>
            <input type="text" 
                id="fecha_finalizacion" 
                class="w-full p-2 border rounded bg-gray-100" 
                readonly 
                placeholder="Selecciona una fecha de inicio primero">
            <p class="text-xs text-gray-500 mt-1">
                La fecha real se calculará al enviar la solicitud y se mostrará en tu panel de Servicio Social.
                Se suman 6 meses exactos a la fecha de inicio, ajustándose si cae en fin de semana.
            </p>
        </div>

        <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
            Enviar solicitud
        </button>
    </form>
</div>

<script>
    function ajustarFechaSiFinDeSemana(fecha) {
        var dia = fecha.getDay();
        if (dia === 6) { // Sábado
            fecha.setDate(fecha.getDate() + 2);
        } else if (dia === 0) { // Domingo
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
            // Crear fecha en UTC para evitar problemas de zona horaria
            var partes = fechaInicioStr.split('-');
            var fecha = new Date(Date.UTC(partes[0], partes[1] - 1, partes[2]));
            
            // Sumar 6 meses
            var mesDestino = fecha.getUTCMonth() + 6;
            var añoDestino = fecha.getUTCFullYear() + Math.floor(mesDestino / 12);
            var mesDestinoFinal = mesDestino % 12;
            
            // Crear nueva fecha con el mismo día
            var nuevaFecha = new Date(Date.UTC(añoDestino, mesDestinoFinal, fecha.getUTCDate()));
            
            // Si el día no existe en el mes destino (ej. 31 de mayo), ajustar al último día del mes
            if (nuevaFecha.getUTCMonth() !== mesDestinoFinal) {
                nuevaFecha.setUTCDate(0); // Último día del mes anterior
            }
            
            nuevaFecha = ajustarFechaSiFinDeSemana(nuevaFecha);
            finalizacionInput.value = formatearFecha(nuevaFecha);
        } else {
            finalizacionInput.value = '';
        }
    });
</script>
@endsection