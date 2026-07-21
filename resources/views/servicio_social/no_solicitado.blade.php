@extends('layouts.app')

@section('content')
<div class="min-h-[60vh] flex items-center justify-center px-4 sm:px-6 py-10">
    <div class="max-w-2xl w-full">

        <!-- Botón Atrás (solo visible en móvil) -->
        <div class="sm:hidden mb-4">
            <a href="{{ route('dashboard') }}" class="inline-flex items-center gap-2 text-green-700 hover:text-green-900 transition text-sm font-medium">
                <span class="iconify w-5 h-5" data-icon="mdi:arrow-left-circle"></span>
                Volver al inicio
            </a>
        </div>

        <!-- ========================================== -->
        <!-- TARJETA PRINCIPAL - ESTILO CONALEP -->
        <!-- ========================================== -->
        <div class="bg-[#f8f8f8] rounded-xl shadow-md border border-gray-200/80 overflow-hidden transition hover:shadow-lg hover:border-green-200">
            
            <!-- Cabecera con icono -->
            <div class="bg-green-50/80 border-b border-gray-200/80 px-6 sm:px-8 py-5 flex items-center gap-4">
                <div class="w-12 h-12 sm:w-14 sm:h-14 bg-green-100 rounded-full flex items-center justify-center flex-shrink-0">
                    <span class="iconify w-7 h-7 sm:w-8 sm:h-8 text-green-600" data-icon="mdi:briefcase-account"></span>
                </div>
                <div>
                    <h2 class="text-lg sm:text-xl font-bold text-green-800">Sin solicitud registrada</h2>
                    <p class="text-xs sm:text-sm text-green-600/80">Servicio Social</p>
                </div>
            </div>

            <!-- Cuerpo -->
            <div class="px-6 sm:px-8 py-6 sm:py-8 space-y-5">
                <!-- Mensaje principal -->
                <div class="bg-white rounded-lg p-4 sm:p-5 border border-gray-200/80">
                    <div class="flex items-start gap-3">
                        <span class="iconify w-5 h-5 text-green-600 flex-shrink-0 mt-0.5" data-icon="mdi:information-outline"></span>
                        <div>
                            <p class="text-sm sm:text-base text-gray-700 leading-relaxed">
                                Aún no has solicitado <strong class="text-green-700">Servicio Social</strong>.
                                Completa el formulario de solicitud para comenzar tu trámite.
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Pasos a seguir -->
                <div class="bg-white rounded-lg p-4 sm:p-5 border border-gray-200/80">
                    <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-3 flex items-center gap-2">
                        <span class="iconify w-4 h-4" data-icon="mdi:list-check"></span>
                        ¿Cómo empezar?
                    </p>
                    <ul class="space-y-2.5 text-sm text-gray-700">
                        <li class="flex items-start gap-3">
                            <span class="w-5 h-5 bg-green-100 rounded-full flex items-center justify-center flex-shrink-0 mt-0.5">
                                <span class="text-green-600 text-xs font-bold">1</span>
                            </span>
                            <span>Completa el formulario de <strong>solicitud</strong></span>
                        </li>
                        <li class="flex items-start gap-3">
                            <span class="w-5 h-5 bg-green-100 rounded-full flex items-center justify-center flex-shrink-0 mt-0.5">
                                <span class="text-green-600 text-xs font-bold">2</span>
                            </span>
                            <span>Sube los <strong>documentos requeridos</strong></span>
                        </li>
                        <li class="flex items-start gap-3">
                            <span class="w-5 h-5 bg-green-100 rounded-full flex items-center justify-center flex-shrink-0 mt-0.5">
                                <span class="text-green-600 text-xs font-bold">3</span>
                            </span>
                            <span>Espera la <strong>validación</strong> y <strong>liberación</strong> del trámite</span>
                        </li>
                    </ul>
                </div>

                <!-- Botón de acción -->
                <div class="pt-2">
                    <a href="{{ route('solicitud-servicio-social.create') }}" 
                       class="inline-flex items-center justify-center gap-2 w-full sm:w-auto px-6 py-3 bg-green-700 hover:bg-green-800 text-white text-sm font-bold rounded-lg transition shadow-md hover:shadow-lg border border-green-800">
                        <span class="iconify w-5 h-5" data-icon="mdi:plus-circle"></span>
                        Ir al formulario
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection