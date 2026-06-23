<x-filament::page>
    <div class="space-y-6">
        <!-- Estadísticas -->
        @livewire(\App\Filament\Widgets\EstadisticasGenerales::class)
        
        <!-- Solicitudes Pendientes -->
        @livewire(\App\Filament\Widgets\SolicitudesPendientes::class)
        
        <!-- Próximas Finalizaciones -->
        @livewire(\App\Filament\Widgets\ProximasFinalizaciones::class)
    </div>
</x-filament::page>