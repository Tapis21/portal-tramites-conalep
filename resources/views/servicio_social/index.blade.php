<!DOCTYPE html>
<html>
<head>
    <title>Mi Servicio Social</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
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
                <p><strong>Estatus:</strong> {{ $servicioSocial->estatus }}</p>

                <!-- Botones de acción: -->
                <div class="mt-4">
                    <a href="{{ route('servicio-social.edit', $servicioSocial->id) }}"
                       class="bg-blue-500 text-white px-4 py-2 rounded">
                        Actualizar horas
                    </a>
                </div>

                @if($servicioSocial->horas_completadas >= 240 && !$servicioSocial->reporte_parcial_subido)
                    <div class="mt-4">
                        <a href="{{ route('servicio-social.subir-reporte-parcial', $servicioSocial->id) }}"
                        class="bg-green-500 text-white px-4 py-2 rounded">
                            Subir reporte parcial
                        </a>
                    </div>
                @endif

                @if($servicioSocial->horas_completadas >= 480 && !$servicioSocial->reporte_final_subido)
                    <div class="mt-4">
                        <a href="{{ route('servicio-social.subir-reporte-final', $servicioSocial->id) }}"
                        class="bg-green-500 text-white px-4 py-2 rounded">
                            Subir reporte final
                        </a>
                    </div>
                @endif

                <div class="mt-4 text-sm">
                    <p>
                        <strong>Reporte parcial:</strong> 
                        @if($servicioSocial->reporte_parcial_subido)
                            <span class="text-green-600">✅ Subido</span>
                        @else
                            <span class="text-yellow-600">⏳ Pendiente (mínimo 240h)</span>
                        @endif
                    </p>
                    <p>
                        <strong>Reporte final:</strong> 
                        @if($servicioSocial->reporte_final_subido)
                            <span class="text-green-600">✅ Subido</span>
                        @else
                            <span class="text-yellow-600">⏳ Pendiente (mínimo 480h)</span>
                        @endif
                    </p>
                </div>

            </div>
        @else
            <p>No hay registro de Servicio Social para este usuario.</p>
        @endif
    </div>
</body>
</html>