<nav class="fixed top-0 left-0 z-40 w-64 h-screen bg-white border-r border-gray-200 hidden sm:block shadow-sm transition-all duration-300">
    <div class="p-4 flex flex-col h-full">
        <!-- Logo con efecto hover -->
        <div class="mb-6 pt-2">
            <a href="{{ route('dashboard') }}" class="block transition-transform duration-300 hover:scale-105">
                <img src="{{ asset('images/Quintana-Roo_3.png') }}" alt="CONALEP" class="h-10 w-auto">
            </a>
        </div>

        <!-- Enlaces del menú -->
        <nav class="space-y-1 flex-1">
            <!-- Dashboard -->
            <a href="{{ route('dashboard') }}" 
               class="group flex items-center gap-3 px-4 py-2.5 rounded-lg transition-all duration-200 relative overflow-hidden
                      {{ request()->routeIs('dashboard') ? 'text-green-700 font-semibold' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                @if(request()->routeIs('dashboard'))
                    <span class="absolute left-0 top-1/2 -translate-y-1/2 w-1 h-6 bg-green-600 rounded-r-full transition-all duration-300"></span>
                @endif
                <span class="iconify w-5 h-5 transition-transform duration-300 group-hover:scale-110 {{ request()->routeIs('dashboard') ? 'text-green-700' : 'text-gray-400 group-hover:text-green-600' }}" 
                      data-icon="mdi:view-dashboard"></span>
                <span class="text-sm transition-colors duration-200">Inicio</span>
                @if(request()->routeIs('dashboard'))
                    <span class="ml-auto w-1.5 h-1.5 bg-green-600 rounded-full animate-pulse"></span>
                @endif
            </a>

            <!-- Servicio Social -->
            <a href="{{ route('servicio-social.index') }}" 
               class="group flex items-center gap-3 px-4 py-2.5 rounded-lg transition-all duration-200 relative overflow-hidden
                      {{ request()->routeIs('servicio-social.*') ? 'text-green-700 font-semibold' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                @if(request()->routeIs('servicio-social.*'))
                    <span class="absolute left-0 top-1/2 -translate-y-1/2 w-1 h-6 bg-green-600 rounded-r-full transition-all duration-300"></span>
                @endif
                <span class="iconify w-5 h-5 transition-transform duration-300 group-hover:scale-110 {{ request()->routeIs('servicio-social.*') ? 'text-green-700' : 'text-gray-400 group-hover:text-green-600' }}" 
                      data-icon="mdi:hand-heart"></span>
                <span class="text-sm transition-colors duration-200">Servicio Social</span>
                @php
                    $user = Auth::user();
                    $badgeSS = 0;

                    if ($user->servicioSocial) {
                        // ✅ SOLO comentarios nuevos de admin en documentos de SS
                        $documentosSSIds = \App\Models\Documento::where('user_id', $user->id)
                            ->whereHas('tipoDocumento', fn($q) => $q->where('tramite', 'SS'))
                            ->pluck('id')
                            ->toArray();

                        $badgeSS = \App\Models\Comentario::where('user_id', $user->id)
                            ->where('tipo', 'admin')
                            ->where('leido', false)
                            ->where('comentable_type', 'App\\Models\\Documento')
                            ->whereIn('comentable_id', $documentosSSIds)
                            ->count();
                    }
                @endphp
                @if($badgeSS > 0)
                    <span class="ml-auto flex items-center justify-center min-w-5 h-5 px-1.5 bg-red-500 text-white text-[10px] font-bold rounded-full animate-pulse">
                        {{ $badgeSS }}
                    </span>
                @endif
            </a>

            <!-- Prácticas -->
            <a href="{{ route('practicas.index') }}" 
               class="group flex items-center gap-3 px-4 py-2.5 rounded-lg transition-all duration-200 relative overflow-hidden
                      {{ request()->routeIs('practicas.*') ? 'text-green-700 font-semibold' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                @if(request()->routeIs('practicas.*'))
                    <span class="absolute left-0 top-1/2 -translate-y-1/2 w-1 h-6 bg-green-600 rounded-r-full transition-all duration-300"></span>
                @endif
                <span class="iconify w-5 h-5 transition-transform duration-300 group-hover:scale-110 {{ request()->routeIs('practicas.*') ? 'text-green-700' : 'text-gray-400 group-hover:text-green-600' }}" 
                      data-icon="mdi:briefcase"></span>
                <span class="text-sm transition-colors duration-200">Prácticas Profesionales</span>
                @php
                    $badgePP = 0;

                    if ($user->practicas) {
                        // ✅ SOLO comentarios nuevos de admin en documentos de PP
                        $documentosPPIds = \App\Models\Documento::where('user_id', $user->id)
                            ->whereHas('tipoDocumento', fn($q) => $q->where('tramite', 'PP'))
                            ->pluck('id')
                            ->toArray();

                        $badgePP = \App\Models\Comentario::where('user_id', $user->id)
                            ->where('tipo', 'admin')
                            ->where('leido', false)
                            ->where('comentable_type', 'App\\Models\\Documento')
                            ->whereIn('comentable_id', $documentosPPIds)
                            ->count();
                    }
                @endphp
                @if($badgePP > 0)
                    <span class="ml-auto flex items-center justify-center min-w-5 h-5 px-1.5 bg-red-500 text-white text-[10px] font-bold rounded-full animate-pulse">
                        {{ $badgePP }}
                    </span>
                @endif
            </a>

            <!-- Separador -->
            <div class="my-3 border-t border-gray-200/60"></div>

            <!-- Perfil -->
            <a href="{{ route('profile.edit') }}" 
               class="group flex items-center gap-3 px-4 py-2.5 rounded-lg transition-all duration-200 relative overflow-hidden
                      {{ request()->routeIs('profile.*') ? 'text-green-700 font-semibold' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                @if(request()->routeIs('profile.*'))
                    <span class="absolute left-0 top-1/2 -translate-y-1/2 w-1 h-6 bg-green-600 rounded-r-full transition-all duration-300"></span>
                @endif
                <span class="iconify w-5 h-5 transition-transform duration-300 group-hover:scale-110 {{ request()->routeIs('profile.*') ? 'text-green-700' : 'text-gray-400 group-hover:text-green-600' }}" 
                      data-icon="mdi:account-cog"></span>
                <span class="text-sm transition-colors duration-200">Mi perfil</span>
            </a>
        </nav>

        <!-- Sección de usuario (DESKTOP) - CLICKEABLE AL PERFIL -->
        <div class="pt-3 border-t border-gray-200">
            <a href="{{ route('profile.edit') }}" 
               class="block px-4 py-2.5 bg-gray-50 rounded-lg mb-2 hover:bg-gray-100 transition-all duration-200 group">
                <div class="flex items-center gap-3">
                    <!-- Avatar con iniciales -->
                    <div class="w-9 h-9 rounded-full bg-green-700 text-white flex items-center justify-center text-sm font-bold shadow-sm flex-shrink-0">
                        {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}{{ strtoupper(substr(Auth::user()->apellidos, 0, 1)) }}
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="font-medium text-gray-800 text-sm truncate group-hover:text-green-700 transition-colors duration-200">
                            {{ Auth::user()->name }}
                        </div>
                        <div class="text-xs text-gray-500 truncate group-hover:text-gray-700 transition-colors duration-200">
                            {{ Auth::user()->email }}
                        </div>
                    </div>
                    <div class="flex items-center gap-1.5">
                        <span class="w-2 h-2 bg-green-500 rounded-full shadow-sm shadow-green-200 flex-shrink-0" title="Conectado"></span>
                        <span class="iconify w-4 h-4 text-gray-400 group-hover:text-green-600 group-hover:translate-x-0.5 transition-all duration-200" data-icon="mdi:chevron-right"></span>
                    </div>
                </div>
            </a>

            <!-- Cerrar sesión -->
            <form method="POST" action="{{ route('logout') }}" class="block mt-1.5">
                @csrf
                <button type="submit" 
                        class="w-full text-left flex items-center gap-3 px-4 py-2.5 rounded-lg transition-all duration-200 text-gray-500 hover:bg-red-50 hover:text-red-600 group">
                    <span class="iconify w-5 h-5 text-gray-400 group-hover:text-red-500 transition-colors duration-200" data-icon="mdi:logout"></span>
                    <span class="text-sm transition-colors duration-200">Cerrar sesión</span>
                    <span class="ml-auto iconify w-4 h-4 text-gray-300 group-hover:translate-x-1 transition-transform duration-200" data-icon="mdi:arrow-right"></span>
                </button>
            </form>
        </div>
    </div>
</nav>

<!-- ========================================== -->
<!-- BARRA SUPERIOR PARA MÓVIL -->
<!-- ========================================== -->
<div class="sm:hidden fixed top-0 left-0 right-0 z-50 bg-white/95 backdrop-blur-sm border-b border-gray-200 shadow-sm">
    <div class="px-4 py-3 flex justify-between items-center">
        <a href="{{ route('dashboard') }}" class="flex items-center gap-2">
            <img src="{{ asset('images/Quintana-Roo_3.png') }}" alt="CONALEP" class="h-8 w-auto">
        </a>
        <button onclick="toggleMobileMenu()" 
                class="p-2 rounded-lg text-gray-500 hover:text-gray-700 hover:bg-gray-100 transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-green-500">
            <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
            </svg>
        </button>
    </div>
</div>

<!-- ========================================== -->
<!-- MENÚ MÓVIL - CLICKEABLE AL PERFIL -->
<!-- ========================================== -->
<div id="mobileMenu" class="sm:hidden fixed inset-0 z-50 bg-white/95 backdrop-blur-sm transform -translate-x-full transition-transform duration-300 ease-in-out shadow-2xl">
    <div class="p-4 flex flex-col h-full">
        <div class="flex justify-between items-center mb-4">
            <a href="{{ route('dashboard') }}">
                <img src="{{ asset('images/Quintana-Roo_3.png') }}" alt="CONALEP" class="h-8 w-auto">
            </a>
            <button onclick="toggleMobileMenu()" 
                    class="p-2 rounded-lg text-gray-500 hover:text-gray-700 hover:bg-gray-100 transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-red-500">
                <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        <!-- Avatar + Perfil (MÓVIL) - CLICKEABLE -->
        <a href="{{ route('profile.edit') }}" onclick="toggleMobileMenu()" 
           class="flex items-center gap-3 px-3 py-3 bg-gray-50 rounded-xl mb-4 hover:bg-gray-100 transition-all duration-200 group">
            <div class="w-11 h-11 rounded-full bg-green-700 text-white flex items-center justify-center text-base font-bold shadow-sm flex-shrink-0">
                {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}{{ strtoupper(substr(Auth::user()->apellidos, 0, 1)) }}
            </div>
            <div class="flex-1 min-w-0">
                <div class="font-medium text-gray-800 text-sm truncate group-hover:text-green-700 transition-colors duration-200">
                    {{ Auth::user()->name }} {{ Auth::user()->apellidos }}
                </div>
                <div class="text-xs text-gray-500 truncate group-hover:text-gray-700 transition-colors duration-200">
                    {{ Auth::user()->email }}
                </div>
            </div>
            <span class="iconify w-4 h-4 text-gray-400 group-hover:text-green-600 group-hover:translate-x-0.5 transition-all duration-200" data-icon="mdi:chevron-right"></span>
        </a>

        <nav class="space-y-1 flex-1">
            <a href="{{ route('dashboard') }}" onclick="toggleMobileMenu()" 
               class="flex items-center gap-3 px-4 py-3 rounded-lg transition-all duration-200 {{ request()->routeIs('dashboard') ? 'bg-green-50 text-green-700 font-semibold' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                <span class="iconify w-5 h-5 {{ request()->routeIs('dashboard') ? 'text-green-700' : 'text-gray-400' }}" data-icon="mdi:view-dashboard"></span>
                <span>Inicio</span>
                @if(request()->routeIs('dashboard'))
                    <span class="ml-auto w-1.5 h-1.5 bg-green-600 rounded-full animate-pulse"></span>
                @endif
            </a>

            <a href="{{ route('servicio-social.index') }}" onclick="toggleMobileMenu()" 
               class="flex items-center gap-3 px-4 py-3 rounded-lg transition-all duration-200 {{ request()->routeIs('servicio-social.*') ? 'bg-green-50 text-green-700 font-semibold' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                <span class="iconify w-5 h-5 {{ request()->routeIs('servicio-social.*') ? 'text-green-700' : 'text-gray-400' }}" data-icon="mdi:hand-heart"></span>
                <span>Servicio Social</span>
                @if($badgeSS ?? 0 > 0)
                    <span class="ml-auto flex items-center justify-center min-w-5 h-5 px-1.5 bg-red-500 text-white text-[10px] font-bold rounded-full animate-pulse">
                        {{ $badgeSS }}
                    </span>
                @endif
            </a>

            <a href="{{ route('practicas.index') }}" onclick="toggleMobileMenu()" 
               class="flex items-center gap-3 px-4 py-3 rounded-lg transition-all duration-200 {{ request()->routeIs('practicas.*') ? 'bg-green-50 text-green-700 font-semibold' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                <span class="iconify w-5 h-5 {{ request()->routeIs('practicas.*') ? 'text-green-700' : 'text-gray-400' }}" data-icon="mdi:briefcase"></span>
                <span>Prácticas</span>
                @if($badgePP ?? 0 > 0)
                    <span class="ml-auto flex items-center justify-center min-w-5 h-5 px-1.5 bg-red-500 text-white text-[10px] font-bold rounded-full animate-pulse">
                        {{ $badgePP }}
                    </span>
                @endif
            </a>

            <div class="my-3 border-t border-gray-200/60"></div>

            <a href="{{ route('profile.edit') }}" onclick="toggleMobileMenu()" 
               class="flex items-center gap-3 px-4 py-3 rounded-lg transition-all duration-200 {{ request()->routeIs('profile.*') ? 'bg-green-50 text-green-700 font-semibold' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                <span class="iconify w-5 h-5 {{ request()->routeIs('profile.*') ? 'text-green-700' : 'text-gray-400' }}" data-icon="mdi:account-cog"></span>
                <span>Mi perfil</span>
            </a>
        </nav>

        <!-- Cerrar sesión en móvil -->
        <div class="pt-4 border-t border-gray-200">
            <form method="POST" action="{{ route('logout') }}" class="block">
                @csrf
                <button type="submit" onclick="toggleMobileMenu()" 
                        class="w-full text-left flex items-center gap-3 px-4 py-3 rounded-lg transition-all duration-200 text-gray-500 hover:bg-red-50 hover:text-red-600">
                    <span class="iconify w-5 h-5 text-gray-400" data-icon="mdi:logout"></span>
                    <span class="text-sm">Cerrar sesión</span>
                </button>
            </form>
        </div>
    </div>
</div>

<!-- ========================================== -->
<!-- JAVASCRIPT PARA MENÚ MÓVIL -->
<!-- ========================================== -->
<script>
    function toggleMobileMenu() {
        const menu = document.getElementById('mobileMenu');
        if (menu.classList.contains('-translate-x-full')) {
            menu.classList.remove('-translate-x-full');
            menu.classList.add('translate-x-0');
            document.body.style.overflow = 'hidden';
        } else {
            menu.classList.remove('translate-x-0');
            menu.classList.add('-translate-x-full');
            document.body.style.overflow = 'auto';
        }
    }

    document.addEventListener('click', function(event) {
        const menu = document.getElementById('mobileMenu');
        const toggleButton = document.querySelector('[onclick="toggleMobileMenu()"]');
        if (menu && toggleButton) {
            if (!menu.contains(event.target) && !toggleButton.contains(event.target) && !menu.classList.contains('-translate-x-full')) {
                toggleMobileMenu();
            }
        }
    });

    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            const menu = document.getElementById('mobileMenu');
            if (menu && !menu.classList.contains('-translate-x-full')) {
                toggleMobileMenu();
            }
        }
    });
</script>

<!-- ========================================== -->
<!-- ESTILOS ADICIONALES -->
<!-- ========================================== -->
<style>
    #mobileMenu {
        transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }

    nav a {
        position: relative;
        transition: all 0.2s ease;
    }

    #mobileMenu .flex-1 {
        overflow-y: auto;
    }

    #mobileMenu .flex-1::-webkit-scrollbar {
        width: 3px;
    }
    #mobileMenu .flex-1::-webkit-scrollbar-track {
        background: transparent;
    }
    #mobileMenu .flex-1::-webkit-scrollbar-thumb {
        background: #d1d5db;
        border-radius: 10px;
    }
    #mobileMenu .flex-1::-webkit-scrollbar-thumb:hover {
        background: #9ca3af;
    }

    nav a:not(.active):hover .iconify {
        filter: drop-shadow(0 0 4px rgba(34, 197, 94, 0.3));
    }

    .absolute.left-0 {
        animation: slideIn 0.3s ease-out;
    }

    @keyframes slideIn {
        from {
            transform: translateY(-50%) scaleY(0);
            opacity: 0;
        }
        to {
            transform: translateY(-50%) scaleY(1);
            opacity: 1;
        }
    }

    .rounded-full.bg-green-700 {
        transition: all 0.3s ease;
    }
    
    .rounded-full.bg-green-700:hover {
        transform: scale(1.05);
        box-shadow: 0 4px 12px rgba(34, 197, 94, 0.3);
    }

    /* Efecto hover en el perfil */
    .group:hover .iconify {
        filter: drop-shadow(0 0 6px rgba(34, 197, 94, 0.2));
    }
</style>