{{-- resources/views/filament/modals/cambiar-estatus-estudiante-modal.blade.php --}}
@php
    $estatuses = [
        'no_solicitado' => ['label' => 'No solicitado', 'color' => '#6b7280', 'bg' => '#f3f4f6'],
        'pendiente' => ['label' => 'Pendiente', 'color' => '#f59e0b', 'bg' => '#fef3c7'],
        'en_progreso' => ['label' => 'En progreso', 'color' => '#2563eb', 'bg' => '#dbeafe'],
        'liberado' => ['label' => 'Liberado', 'color' => '#059669', 'bg' => '#d1fae5'],
    ];
    
    $estatusActual = $this->record->estatus ?? 'no_solicitado';
@endphp

<div style="padding: 8px;">

    <!-- Encabezado -->
    <div style="display: flex; align-items: center; gap: 16px; border-bottom: 2px solid #e5e7eb; padding-bottom: 16px; margin-bottom: 16px;">
        <div style="display: flex; align-items: center; gap: 12px;">
            <div style="padding: 12px; background: #e0e7ff; border-radius: 12px; display: flex; align-items: center; justify-content: center;">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#4f46e5" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
                    <polyline points="9 12 11 14 15 10"/>
                </svg>
            </div>
            <div>
                <h3 style="font-size: 20px; font-weight: bold; color: #111827; margin: 0;">Actualizar Estatus del Estudiante</h3>
                <p style="font-size: 13px; color: #6b7280; margin: 0;">{{ $record->user->name }}</p>
            </div>
        </div>
    </div>

    <!-- Información actual -->
    <div style="background: #f3f4f6; border-radius: 8px; padding: 12px 16px; margin-bottom: 16px;">
        <div style="display: flex; justify-content: space-between; align-items: center;">
            <span style="font-size: 13px; font-weight: 500; color: #6b7280;">Estatus actual</span>
            <span style="display: inline-block; padding: 4px 12px; border-radius: 9999px; font-size: 12px; font-weight: 500; background: {{ $estatuses[$estatusActual]['bg'] ?? '#f3f4f6' }}; color: {{ $estatuses[$estatusActual]['color'] ?? '#6b7280' }};">
                {{ $estatuses[$estatusActual]['label'] ?? $estatusActual }}
            </span>
        </div>
    </div>

    <!-- Formulario con Livewire -->
    <form wire:submit.prevent="actualizarEstatusEstudiante">
        <div style="margin-bottom: 16px;">
            <label for="nuevo_estatus_estudiante" style="display: block; font-size: 13px; font-weight: 600; color: #374151; margin-bottom: 6px;">
                Nuevo Estatus
            </label>
            <select 
                wire:model="nuevo_estatus_estudiante" 
                id="nuevo_estatus_estudiante" 
                style="width: 100%; padding: 10px 12px; border: 1px solid #d1d5db; border-radius: 8px; font-size: 14px; background: white; outline: none; transition: border-color 0.2s; cursor: pointer;"
            >
                <option value="">Selecciona un estatus...</option>
                @foreach($estatuses as $key => $estatus)
                    <option value="{{ $key }}" {{ $estatusActual === $key ? 'selected' : '' }}>
                        {{ $estatus['label'] }}
                    </option>
                @endforeach
            </select>
        </div>

        <div style="margin-bottom: 16px;">
            <label for="comentario_estatus_estudiante" style="display: block; font-size: 13px; font-weight: 600; color: #374151; margin-bottom: 6px;">
                Comentario (opcional)
            </label>
            <textarea 
                wire:model="comentario_estatus_estudiante" 
                id="comentario_estatus_estudiante" 
                rows="3" 
                style="width: 100%; padding: 12px; border: 1px solid #d1d5db; border-radius: 8px; font-size: 14px; font-family: inherit; outline: none; transition: border-color 0.2s; resize: vertical;"
                placeholder="Agrega un comentario sobre este cambio de estatus..."
            ></textarea>
        </div>

        <div style="display: flex; justify-content: flex-end; gap: 8px; padding-top: 8px; border-top: 1px solid #e5e7eb;">
            <button 
                type="button" 
                wire:click="$dispatch('close-modal', { id: 'cambiar-estatus-estudiante' })" 
                style="padding: 8px 20px; border-radius: 8px; font-size: 14px; font-weight: 500; color: #6b7280; background: #f3f4f6; border: 1px solid #e5e7eb; cursor: pointer; transition: all 0.2s;"
                onmouseover="this.style.backgroundColor='#e5e7eb'"
                onmouseout="this.style.backgroundColor='#f3f4f6'"
            >
                Cerrar
            </button>
            <button 
                type="submit" 
                style="padding: 8px 20px; border-radius: 8px; font-size: 14px; font-weight: 500; color: white; background: #4f46e5; border: none; cursor: pointer; display: inline-flex; align-items: center; gap: 8px; transition: all 0.2s;"
                onmouseover="this.style.backgroundColor='#4338ca'"
                onmouseout="this.style.backgroundColor='#4f46e5'"
            >
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M20 14.66V20a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h5.34"/>
                    <polygon points="18 2 22 6 12 16 8 16 8 12 18 2"/>
                </svg>
                Actualizar Estatus
            </button>
        </div>
    </form>

</div>