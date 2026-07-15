<x-guest-layout>
    <div class="w-full max-w-sm sm:max-w-md bg-white/80 backdrop-blur-xl border border-white/50 rounded-2xl sm:rounded-3xl p-5 sm:p-7 md:p-8 shadow-[0_4px_6px_-1px_rgba(0,0,0,0.05),0_20px_40px_-10px_rgba(0,104,55,0.1),inset_0_0_0_1px_rgba(255,255,255,0.6)] opacity-0 translate-y-10 animate-[slideUp_0.8s_cubic-bezier(0.16,1,0.3,1)_forwards]">

        <!-- Flecha de regreso -->
        <div class="flex items-center justify-between mb-4 sm:mb-6">
            <a href="{{ route('home') }}" class="inline-flex items-center gap-1.5 text-sm text-gray-500 hover:text-green-700 transition-colors duration-200">
                <span class="iconify w-4 h-4" data-icon="mdi:arrow-left"></span>
                Volver al inicio
            </a>
            <span class="text-xs text-gray-400">Registro</span>
        </div>

        <!-- Título -->
        <div class="text-center mb-4 sm:mb-6">
            <h2 class="text-xl sm:text-2xl font-bold text-gray-800">Crear cuenta</h2>
            <p class="text-xs text-gray-500 mt-1">El registro debe realizarse de forma presencial</p>
        </div>

        <!-- Modal de información -->
        <div class="bg-amber-50 border-l-4 border-amber-500 text-amber-700 p-4 rounded-lg mb-4 text-sm flex items-start gap-3">
            <span class="iconify w-5 h-5 flex-shrink-0 mt-0.5 text-amber-500" data-icon="mdi:information"></span>
            <div>
                <p class="font-medium">Registro en ventanilla</p>
                <p class="text-xs mt-1">Para crear tu cuenta, debes acercarte al departamento de servicios escolares con tu matrícula y documentos oficiales.</p>
                <a href="{{ route('home') }}" class="inline-flex items-center gap-1 mt-2 text-amber-700 hover:text-amber-800 font-medium text-xs transition">
                    Volver al inicio
                    <span class="iconify w-3 h-3" data-icon="mdi:arrow-right"></span>
                </a>
            </div>
        </div>

        <!-- Información adicional -->
        <div class="text-center text-xs text-gray-400 mt-4">
            <p>¿Ya tienes cuenta? <a href="{{ route('login') }}" class="text-green-700 hover:text-green-800 font-medium transition">Inicia sesión aquí</a></p>
        </div>

        <!-- Footer -->
        @include('partials.footer')
    </div>
</x-guest-layout>