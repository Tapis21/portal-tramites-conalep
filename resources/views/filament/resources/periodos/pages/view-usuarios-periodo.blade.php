<x-filament-panels::page>
    <div class="space-y-4">
        <div class="bg-blue-50 dark:bg-blue-900/20 border-l-4 border-blue-500 p-4 rounded-lg">
            <div class="flex items-center gap-3">
                <span class="text-xl">📌</span>
                <div>
                    <h4 class="font-bold text-blue-800 dark:text-blue-400">Usuarios del periodo: {{ $record->nombre }}</h4>
                    <p class="text-sm text-blue-600 dark:text-blue-300">
                        Mostrando {{ $this->table->getRecords()->count() }} usuarios de este periodo.
                        <span class="text-xs text-blue-500">
                            {{ $record->activo ? '✅ Periodo activo' : '❌ Periodo inactivo' }}
                        </span>
                    </p>
                </div>
            </div>
        </div>

        {{ $this->table }}
    </div>
</x-filament-panels::page>