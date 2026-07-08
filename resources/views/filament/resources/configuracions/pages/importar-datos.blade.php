<x-filament-panels::page>
    <form wire:submit="importar" id="importar-form">
        <div class="space-y-6">
            {{ $this->form }}
        </div>
        {{-- El botón de importar se muestra automáticamente desde getFormActions() --}}
    </form>
</x-filament-panels::page>