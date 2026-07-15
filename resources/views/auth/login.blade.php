<x-guest-layout>
    <div class="w-full max-w-sm sm:max-w-md bg-white/80 backdrop-blur-xl border border-white/50 rounded-2xl sm:rounded-3xl p-5 sm:p-7 md:p-8 shadow-[0_4px_6px_-1px_rgba(0,0,0,0.05),0_20px_40px_-10px_rgba(0,104,55,0.1),inset_0_0_0_1px_rgba(255,255,255,0.6)] opacity-0 translate-y-10 animate-[slideUp_0.8s_cubic-bezier(0.16,1,0.3,1)_forwards]">

        <!-- Flecha de regreso -->
        <div class="flex items-center justify-between mb-4 sm:mb-6">
            <a href="{{ route('home') }}" class="inline-flex items-center gap-1.5 text-sm text-gray-500 hover:text-green-700 transition-colors duration-200">
                <span class="iconify w-4 h-4" data-icon="mdi:arrow-left"></span>
                Volver al inicio
            </a>
            <span class="text-xs text-gray-400">Iniciar sesión</span>
        </div>

        <!-- Título -->
        <div class="text-center mb-4 sm:mb-6">
            <h2 class="text-xl sm:text-2xl font-bold text-gray-800">Bienvenido de vuelta</h2>
            <p class="text-xs text-gray-500 mt-1">Accede a tu cuenta para gestionar tus trámites</p>
        </div>

        <!-- Alertas -->
        @if(session('status'))
            <div class="bg-green-50 border-l-4 border-green-500 text-green-700 p-3 rounded-lg mb-4 text-sm flex items-start gap-2">
                <span class="iconify w-4 h-4 flex-shrink-0 mt-0.5 text-green-500" data-icon="mdi:check-circle"></span>
                {{ session('status') }}
            </div>
        @endif

        @if($errors->any())
            <div class="bg-red-50 border-l-4 border-red-500 text-red-700 p-3 rounded-lg mb-4 text-sm flex items-start gap-2">
                <span class="iconify w-4 h-4 flex-shrink-0 mt-0.5 text-red-500" data-icon="mdi:alert-circle"></span>
                <div>
                    <p class="font-medium">Error de autenticación</p>
                    <ul class="text-xs list-disc list-inside mt-1">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        @endif

        <!-- Formulario -->
        <form method="POST" action="{{ route('login') }}" class="space-y-4">
            @csrf

            <!-- Email -->
            <div>
                <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Correo electrónico</label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none text-gray-400">
                        <span class="iconify w-4 h-4" data-icon="mdi:email"></span>
                    </span>
                    <input type="email" name="email" id="email" value="{{ old('email') }}" required autofocus
                           class="w-full pl-9 pr-3 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-green-500 transition bg-white/90 text-sm"
                           placeholder="ejemplo@conalep.edu.mx">
                </div>
                @error('email')
                    <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Contraseña -->
            <div>
                <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Contraseña</label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none text-gray-400">
                        <span class="iconify w-4 h-4" data-icon="mdi:lock"></span>
                    </span>
                    <input type="password" name="password" id="password" required
                           class="w-full pl-9 pr-9 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-green-500 transition bg-white/90 text-sm"
                           placeholder="••••••••">
                    <button type="button" onclick="togglePassword()" class="absolute inset-y-0 right-0 flex items-center pr-3 text-gray-400 hover:text-gray-600 transition">
                        <span class="iconify w-5 h-5" id="toggleIcon" data-icon="mdi:eye"></span>
                    </button>
                </div>
                @error('password')
                    <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Opciones -->
            <div class="flex items-center justify-between flex-wrap gap-2">
                <label class="flex items-center gap-2 text-sm text-gray-600">
                    <input type="checkbox" name="remember" class="w-4 h-4 text-green-600 border-gray-300 rounded focus:ring-green-500">
                    <span>Recordarme</span>
                </label>
                @if(Route::has('password.request'))
                    <a href="{{ route('password.request') }}" class="text-sm font-medium text-green-700 hover:text-green-800 transition">¿Olvidaste tu contraseña?</a>
                @endif
            </div>

            <!-- Botón -->
            <button type="submit" class="w-full flex items-center justify-center gap-2 px-4 py-3 bg-gradient-to-br from-green-700 to-green-600 hover:from-green-800 hover:to-green-700 text-white font-semibold rounded-xl transition-all duration-300 shadow-md hover:shadow-lg hover:-translate-y-0.5 active:translate-y-0 border border-green-700/20">
                <span class="iconify w-5 h-5" data-icon="mdi:login"></span>
                Iniciar sesión
            </button>

            <!-- Registro -->
            <div class="text-center text-sm text-gray-600 pt-2">
                ¿No tienes cuenta?
                <a href="{{ route('register') }}" class="text-green-700 hover:text-green-800 font-medium transition">Regístrate aquí</a>
            </div>
        </form>

        <!-- Footer -->
        @include('partials.footer')
    </div>

    <!-- Script para toggle de contraseña -->
    <script>
        function togglePassword() {
            const passwordInput = document.getElementById('password');
            const toggleIcon = document.getElementById('toggleIcon');
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                toggleIcon.setAttribute('data-icon', 'mdi:eye-off');
            } else {
                passwordInput.type = 'password';
                toggleIcon.setAttribute('data-icon', 'mdi:eye');
            }
        }
    </script>
</x-guest-layout>