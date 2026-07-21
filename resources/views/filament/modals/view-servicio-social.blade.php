<x-filament-panels::page>
    {{-- Infolist --}}
    <div class="space-y-4">
        <div class="bg-white rounded-lg shadow">
            {{ $this->infolist }}
        </div>
    </div>

    {{-- Tabla de Documentos --}}
    <div class="mt-6">
        <div class="bg-white rounded-lg shadow">
            <div class="p-4 border-b">
                <h3 class="text-lg font-semibold">📄 Documentos Subidos</h3>
            </div>
            <div class="p-4">
                {{ $this->table }}
            </div>
        </div>
    </div>
</x-filament-panels::page>
