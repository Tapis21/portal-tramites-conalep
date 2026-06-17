<nav class="fixed top-0 left-0 z-40 w-64 h-screen bg-white border-r border-gray-200 hidden sm:block">
    <div class="p-4 flex flex-col h-full">
        <!-- Logo -->
        <div class="mb-8 pt-2">
            <a href="{{ route('dashboard') }}">
                <img src="{{ asset('images/Quintana-Roo_3.png') }}" alt="CONALEP" class="h-10 w-auto">
            </a>
        </div>

        <!-- Enlaces del menú -->
        <nav class="space-y-1.5 flex-1">
            <a href="{{ route('dashboard') }}" 
               class="flex items-center gap-3 px-4 py-3 rounded-lg transition-all duration-200 {{ request()->routeIs('dashboard') ? 'bg-green-50 text-green-800 font-medium' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                <span class="iconify w-5 h-5 {{ request()->routeIs('dashboard') ? 'text-green-700' : 'text-gray-400' }}" data-icon="mdi:view-dashboard"></span>
                <span>Inicio</span>
            </a>

            <a href="{{ route('servicio-social.index') }}" 
               class="flex items-center gap-3 px-4 py-3 rounded-lg transition-all duration-200 {{ request()->routeIs('servicio-social.*') ? 'bg-green-50 text-green-800 font-medium' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                <span class="iconify w-5 h-5 {{ request()->routeIs('servicio-social.*') ? 'text-green-700' : 'text-gray-400' }}" data-icon="mdi:hand-heart"></span>
                <span>Servicio Social</span>
            </a>

            <a href="{{ route('practicas.index') }}" 
               class="flex items-center gap-3 px-4 py-3 rounded-lg transition-all duration-200 {{ request()->routeIs('practicas.*') ? 'bg-green-50 text-green-800 font-medium' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                <span class="iconify w-5 h-5 {{ request()->routeIs('practicas.*') ? 'text-green-700' : 'text-gray-400' }}" data-icon="mdi:briefcase"></span>
                <span>Prácticas Profesionales</span>
            </a>

            <!-- Separador visual -->
            <div class="my-4 border-t border-gray-200/60"></div>

            <!-- Enlace de perfil en el menú (visible en desktop) -->
            <a href="{{ route('profile.edit') }}" 
               class="flex items-center gap-3 px-4 py-3 rounded-lg transition-all duration-200 text-gray-600 hover:bg-gray-50 hover:text-gray-900">
                <span class="iconify w-5 h-5 text-gray-400" data-icon="mdi:account-cog"></span>
                <span>Mi perfil</span>
            </a>
        </nav>

        <!-- Sección de usuario (abajo) -->
        <div class="pt-4 border-t border-gray-200">
            <div class="px-4 py-2.5 bg-gray-50 rounded-lg mb-2">
                <div class="font-medium text-gray-800 text-sm truncate">{{ Auth::user()->name }}</div>
                <div class="text-xs text-gray-500 truncate">{{ Auth::user()->email }}</div>
            </div>
            <form method="POST" action="{{ route('logout') }}" class="block">
                @csrf
                <button type="submit" class="w-full text-left flex items-center gap-3 px-4 py-3 rounded-lg transition-all duration-200 text-gray-600 hover:bg-red-50 hover:text-red-600">
                    <span class="iconify w-5 h-5 text-gray-400" data-icon="mdi:logout"></span>
                    <span class="text-sm">Cerrar sesión</span>
                </button>
            </form>
        </div>
    </div>
</nav>

<!-- ========================================== -->
<!-- BARRA SUPERIOR PARA MÓVIL -->
<!-- ========================================== -->
<div class="sm:hidden fixed top-0 left-0 right-0 z-50 bg-white border-b border-gray-200 shadow-sm">
    <div class="px-4 py-3 flex justify-between items-center">
        <a href="{{ route('dashboard') }}">
            <img src="{{ asset('images/Quintana-Roo_3.png') }}" alt="CONALEP" class="h-8 w-auto">
        </a>
        <button onclick="toggleMobileMenu()" class="p-2 rounded-md text-gray-500 hover:text-gray-700 hover:bg-gray-100 transition">
            <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
            </svg>
        </button>
    </div>
</div>

<!-- ========================================== -->
<!-- MENÚ MÓVIL (OCULTO POR DEFECTO) -->
<!-- ========================================== -->
<div id="mobileMenu" class="sm:hidden fixed inset-0 z-50 bg-white transform -translate-x-full transition-transform duration-300 ease-in-out shadow-xl">
    <div class="p-4 flex flex-col h-full">
        <div class="flex justify-between items-center mb-6">
            <a href="{{ route('dashboard') }}">
                <img src="{{ asset('images/Quintana-Roo_3.png') }}" alt="CONALEP" class="h-8 w-auto">
            </a>
            <button onclick="toggleMobileMenu()" class="p-2 rounded-md text-gray-500 hover:text-gray-700 hover:bg-gray-100 transition">
                <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        <nav class="space-y-1.5 flex-1">
            <a href="{{ route('dashboard') }}" onclick="toggleMobileMenu()" 
               class="flex items-center gap-3 px-4 py-3 rounded-lg transition-all duration-200 {{ request()->routeIs('dashboard') ? 'bg-green-50 text-green-800 font-medium' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                <span class="iconify w-5 h-5 {{ request()->routeIs('dashboard') ? 'text-green-700' : 'text-gray-400' }}" data-icon="mdi:view-dashboard"></span>
                <span>Inicio</span>
            </a>

            <a href="{{ route('servicio-social.index') }}" onclick="toggleMobileMenu()" 
               class="flex items-center gap-3 px-4 py-3 rounded-lg transition-all duration-200 {{ request()->routeIs('servicio-social.*') ? 'bg-green-50 text-green-800 font-medium' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                <span class="iconify w-5 h-5 {{ request()->routeIs('servicio-social.*') ? 'text-green-700' : 'text-gray-400' }}" data-icon="mdi:hand-heart"></span>
                <span>Servicio Social</span>
            </a>

            <a href="{{ route('practicas.index') }}" onclick="toggleMobileMenu()" 
               class="flex items-center gap-3 px-4 py-3 rounded-lg transition-all duration-200 {{ request()->routeIs('practicas.*') ? 'bg-green-50 text-green-800 font-medium' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                <span class="iconify w-5 h-5 {{ request()->routeIs('practicas.*') ? 'text-green-700' : 'text-gray-400' }}" data-icon="mdi:briefcase"></span>
                <span>Prácticas Profesionales</span>
            </a>

            <!-- Separador visual -->
            <div class="my-4 border-t border-gray-200/60"></div>

            <a href="{{ route('profile.edit') }}" onclick="toggleMobileMenu()" 
               class="flex items-center gap-3 px-4 py-3 rounded-lg transition-all duration-200 text-gray-600 hover:bg-gray-50 hover:text-gray-900">
                <span class="iconify w-5 h-5 text-gray-400" data-icon="mdi:account-cog"></span>
                <span>Mi perfil</span>
            </a>
        </nav>

        <div class="pt-4 border-t border-gray-200">
            <div class="px-4 py-2.5 bg-gray-50 rounded-lg mb-2">
                <div class="font-medium text-gray-800 text-sm truncate">{{ Auth::user()->name }}</div>
                <div class="text-xs text-gray-500 truncate">{{ Auth::user()->email }}</div>
            </div>
            <form method="POST" action="{{ route('logout') }}" class="block">
                @csrf
                <button type="submit" class="w-full text-left flex items-center gap-3 px-4 py-3 rounded-lg transition-all duration-200 text-gray-600 hover:bg-red-50 hover:text-red-600">
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

    // Cerrar menú al hacer clic fuera (opcional)
    document.addEventListener('click', function(event) {
        const menu = document.getElementById('mobileMenu');
        const toggleButton = document.querySelector('[onclick="toggleMobileMenu()"]');
        if (menu && toggleButton) {
            if (!menu.contains(event.target) && !toggleButton.contains(event.target) && !menu.classList.contains('-translate-x-full')) {
                toggleMobileMenu();
            }
        }
    });
</script>

<!-- ========================================== -->
<!-- ESTILOS ADICIONALES PARA LA NAVEGACIÓN -->
<!-- ========================================== -->
<style>
    /* Transición suave para el menú móvil */
    #mobileMenu {
        transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }
    
    /* Efecto hover en enlaces */
    nav a {
        position: relative;
    }
    
    /* Scroll suave en el menú móvil */
    #mobileMenu .flex-1 {
        overflow-y: auto;
    }
    
    /* Scrollbar personalizado para el menú móvil */
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
</style>