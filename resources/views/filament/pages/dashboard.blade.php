<x-filament::page>
    <div class="space-y-6">
        @livewire(\App\Filament\Widgets\EstadisticasGenerales::class)
        @livewire(\App\Filament\Widgets\SolicitudesPendientesSS::class)
        @livewire(\App\Filament\Widgets\SolicitudesPendientesPP::class)
        @livewire(\App\Filament\Widgets\ProximasFinalizacionesSS::class)
        @livewire(\App\Filament\Widgets\ProximasFinalizacionesPP::class)
    </div>
</x-filament::page>