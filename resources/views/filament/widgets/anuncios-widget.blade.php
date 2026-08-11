<x-filament::widget>
    <x-filament::card>
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-lg font-semibold text-gray-900 flex items-center gap-2">
                <span class="iconify w-5 h-5 text-primary-600" data-icon="mdi:bullhorn"></span>
                Anuncios importantes
            </h2>
            <span class="text-xs text-gray-500">
                Ultimos anuncios
            </span>
        </div>

        @if($anuncios->count() > 0)
            <div class="space-y-4">
                @foreach($anuncios as $anuncio)
                    <div class="p-4 bg-gradient-to-r from-gray-50 to-white rounded-lg border border-gray-200 hover:shadow-md transition-all duration-200">
                        <div class="flex items-start gap-3">
                            <div class="flex-shrink-0 mt-0.5">
                                <span class="iconify w-5 h-5 text-blue-500" data-icon="mdi:bullhorn"></span>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm text-gray-800 leading-relaxed">{{ $anuncio->contenido }}</p>
                                <div class="flex flex-wrap items-center gap-3 mt-2">
                                    <span class="text-xs text-gray-400 flex items-center gap-1">
                                        <span class="iconify w-3 h-3" data-icon="mdi:calendar"></span>
                                        {{ $anuncio->created_at->format('d/m/Y H:i') }}
                                    </span>
                                    @if($anuncio->admin)
                                        <span class="text-xs text-gray-400 flex items-center gap-1">
                                            <span class="iconify w-3 h-3" data-icon="mdi:account"></span>
                                            {{ $anuncio->admin->name }}
                                        </span>
                                    @endif
                                </div>
                            </div>
                            @if(!$anuncio->vistoPor($user))
                                <div class="flex-shrink-0">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-700">
                                        Nuevo
                                    </span>
                                </div>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="text-center py-10">
                <div class="w-16 h-16 rounded-full bg-gray-100 flex items-center justify-center mx-auto mb-3">
                    <span class="iconify w-8 h-8 text-gray-400" data-icon="mdi:bullhorn-off"></span>
                </div>
                <p class="text-sm text-gray-500">No hay anuncios disponibles</p>
                <p class="text-xs text-gray-400 mt-1">Los anuncios apareceran aqui cuando el administrador los publique.</p>
            </div>
        @endif
    </x-filament::card>
</x-filament::widget>