<nav class="fixed top-0 left-0 z-40 w-64 h-screen bg-white shadow-lg border-r border-gray-200 hidden sm:block">
    <div class="p-4">
        <!-- Logo -->
        <div class="mb-6">
            <a href="{{ route('dashboard') }}">
                <x-application-logo class="block h-9 w-auto fill-current text-gray-800" />
            </a>
        </div>

        <!-- Enlaces del menú -->
        <nav class="space-y-2">
            <a href="{{ route('dashboard') }}" 
               class="flex items-center gap-3 p-3 rounded-lg hover:bg-gray-100 {{ request()->routeIs('dashboard') ? 'bg-gray-100 text-blue-600' : 'text-gray-700' }}">
                <span class="iconify w-5 h-5" data-icon="mdi:view-dashboard"></span>
                <span>Inicio</span>
            </a>

            <a href="{{ route('servicio-social.index') }}" 
               class="flex items-center gap-3 p-3 rounded-lg hover:bg-gray-100 {{ request()->routeIs('servicio-social.*') ? 'bg-gray-100 text-blue-600' : 'text-gray-700' }}">
                <span class="iconify w-5 h-5" data-icon="mdi:hand-heart"></span>
                <span>Servicio Social</span>
            </a>

            <a href="{{ route('practicas.index') }}" 
               class="flex items-center gap-3 p-3 rounded-lg hover:bg-gray-100 {{ request()->routeIs('practicas.*') ? 'bg-gray-100 text-blue-600' : 'text-gray-700' }}">
                <span class="iconify w-5 h-5" data-icon="mdi:briefcase"></span>
                <span>Prácticas Profesionales</span>
            </a>
        </nav>

        <!-- Sección de usuario -->
        <div class="absolute bottom-0 left-0 right-0 p-4 border-t border-gray-200">
            <div class="px-3 py-2">
                <div class="font-medium text-gray-800">{{ Auth::user()->name }}</div>
                <div class="text-sm text-gray-500">{{ Auth::user()->email }}</div>
            </div>
            <a href="{{ route('profile.edit') }}" 
               class="flex items-center gap-3 p-3 rounded-lg hover:bg-gray-100 text-gray-700">
                <span class="iconify w-5 h-5" data-icon="mdi:account-cog"></span>
                <span>Mi perfil</span>
            </a>
            <form method="POST" action="{{ route('logout') }}" class="block">
                @csrf
                <button type="submit" class="w-full text-left flex items-center gap-3 p-3 rounded-lg hover:bg-gray-100 text-gray-700">
                    <span class="iconify w-5 h-5" data-icon="mdi:logout"></span>
                    <span>Cerrar sesión</span>
                </button>
            </form>
        </div>
    </div>
</nav>

<!-- Barra superior para móvil -->
<div class="sm:hidden fixed top-0 left-0 right-0 z-50 bg-white border-b border-gray-200">
    <div class="px-4 py-3 flex justify-between items-center">
        <a href="{{ route('dashboard') }}">
            <x-application-logo class="block h-8 w-auto fill-current text-gray-800" />
        </a>
        <button onclick="toggleMobileMenu()" class="p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100">
            <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
            </svg>
        </button>
    </div>
</div>

<!-- Menú móvil (oculto por defecto) -->
<div id="mobileMenu" class="sm:hidden fixed inset-0 z-50 bg-white transform -translate-x-full transition-transform duration-300 ease-in-out">
    <div class="p-4">
        <div class="flex justify-between items-center mb-6">
            <a href="{{ route('dashboard') }}">
                <x-application-logo class="block h-8 w-auto fill-current text-gray-800" />
            </a>
            <button onclick="toggleMobileMenu()" class="p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100">
                <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        <nav class="space-y-2">
            <a href="{{ route('dashboard') }}" onclick="toggleMobileMenu()" 
               class="flex items-center gap-3 p-3 rounded-lg hover:bg-gray-100 {{ request()->routeIs('dashboard') ? 'bg-gray-100 text-blue-600' : 'text-gray-700' }}">
                <span class="iconify w-5 h-5" data-icon="mdi:view-dashboard"></span>
                <span>Inicio</span>
            </a>

            <a href="{{ route('servicio-social.index') }}" onclick="toggleMobileMenu()" 
               class="flex items-center gap-3 p-3 rounded-lg hover:bg-gray-100 {{ request()->routeIs('servicio-social.*') ? 'bg-gray-100 text-blue-600' : 'text-gray-700' }}">
                <span class="iconify w-5 h-5" data-icon="mdi:hand-heart"></span>
                <span>Servicio Social</span>
            </a>

            <a href="{{ route('practicas.index') }}" onclick="toggleMobileMenu()" 
               class="flex items-center gap-3 p-3 rounded-lg hover:bg-gray-100 {{ request()->routeIs('practicas.*') ? 'bg-gray-100 text-blue-600' : 'text-gray-700' }}">
                <span class="iconify w-5 h-5" data-icon="mdi:briefcase"></span>
                <span>Prácticas Profesionales</span>
            </a>
        </nav>

        <div class="absolute bottom-0 left-0 right-0 p-4 border-t border-gray-200">
            <div class="px-3 py-2">
                <div class="font-medium text-gray-800">{{ Auth::user()->name }}</div>
                <div class="text-sm text-gray-500">{{ Auth::user()->email }}</div>
            </div>
            <a href="{{ route('profile.edit') }}" onclick="toggleMobileMenu()" 
               class="flex items-center gap-3 p-3 rounded-lg hover:bg-gray-100 text-gray-700">
                <span class="iconify w-5 h-5" data-icon="mdi:account-cog"></span>
                <span>Mi perfil</span>
            </a>
            <form method="POST" action="{{ route('logout') }}" class="block">
                @csrf
                <button type="submit" class="w-full text-left flex items-center gap-3 p-3 rounded-lg hover:bg-gray-100 text-gray-700">
                    <span class="iconify w-5 h-5" data-icon="mdi:logout"></span>
                    <span>Cerrar sesión</span>
                </button>
            </form>
        </div>
    </div>
</div>

<script>
    function toggleMobileMenu() {
        const menu = document.getElementById('mobileMenu');
        if (menu.classList.contains('-translate-x-full')) {
            menu.classList.remove('-translate-x-full');
            menu.classList.add('translate-x-0');
        } else {
            menu.classList.remove('translate-x-0');
            menu.classList.add('-translate-x-full');
        }
    }
</script>