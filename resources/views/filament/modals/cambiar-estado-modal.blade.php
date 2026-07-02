{{-- resources/views/filament/modals/cambiar-estado-modal.blade.php --}}
@php
    $estados = [
        'pendiente' => ['label' => 'Pendiente', 'color' => '#f59e0b', 'bg' => '#fef3c7'],
        'validado' => ['label' => 'Validado', 'color' => '#059669', 'bg' => '#d1fae5'],
        'validado_ventanilla' => ['label' => 'Validado en Ventanilla', 'color' => '#2563eb', 'bg' => '#dbeafe'],
        'rechazado' => ['label' => 'Rechazado', 'color' => '#dc2626', 'bg' => '#fee2e2'],
    ];
@endphp

<div style="padding: 8px;">

    <!-- Encabezado -->
    <div style="display: flex; align-items: center; gap: 16px; border-bottom: 2px solid #e5e7eb; padding-bottom: 16px; margin-bottom: 16px;">
        <div style="display: flex; align-items: center; gap: 12px;">
            <div style="padding: 12px; background: #fef3c7; border-radius: 12px; display: flex; align-items: center; justify-content: center;">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#d97706" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M12 20h9"/>
                    <path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"/>
                </svg>
            </div>
            <div>
                <h3 style="font-size: 20px; font-weight: bold; color: #111827; margin: 0;">Cambiar Estado</h3>
                <p style="font-size: 13px; color: #6b7280; margin: 0;">{{ $record->tipoDocumento->nombre }}</p>
            </div>
        </div>
    </div>

    <!-- Formulario con Livewire -->
    <form wire:submit.prevent="cambiarEstado({{ $documentoId }})">
        <div style="margin-bottom: 16px;">
            <label for="nuevo_estatus" style="display: block; font-size: 13px; font-weight: 600; color: #374151; margin-bottom: 6px;">
                Nuevo Estado
            </label>
            <select 
                wire:model="nuevo_estatus" 
                id="nuevo_estatus" 
                style="width: 100%; padding: 10px 12px; border: 1px solid #d1d5db; border-radius: 8px; font-size: 14px; background: white; outline: none; transition: border-color 0.2s; cursor: pointer;"
            >
                <option value="">Selecciona un estado...</option>
                @foreach($estados as $key => $estado)
                    <option value="{{ $key }}" {{ $record->estatus === $key ? 'selected' : '' }}>
                        {{ $estado['label'] }}
                    </option>
                @endforeach
            </select>
        </div>

        <div style="margin-bottom: 16px;">
            <label for="comentario_estado" style="display: block; font-size: 13px; font-weight: 600; color: #374151; margin-bottom: 6px;">
                Comentario (opcional)
            </label>
            <textarea 
                wire:model="comentario_estado" 
                id="comentario_estado" 
                rows="3" 
                style="width: 100%; padding: 12px; border: 1px solid #d1d5db; border-radius: 8px; font-size: 14px; font-family: inherit; outline: none; transition: border-color 0.2s; resize: vertical;"
                placeholder="Agrega un comentario sobre este cambio de estado..."
            ></textarea>
        </div>

        <div style="display: flex; justify-content: flex-end; gap: 8px; padding-top: 8px; border-top: 1px solid #e5e7eb;">
            <button 
                type="submit" 
                style="padding: 8px 20px; border-radius: 8px; font-size: 14px; font-weight: 500; color: white; background: #d97706; border: none; cursor: pointer; display: inline-flex; align-items: center; gap: 8px; transition: all 0.2s;"
                onmouseover="this.style.backgroundColor='#b45309'"
                onmouseout="this.style.backgroundColor='#d97706'"
            >
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M20 14.66V20a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h5.34"/>
                    <polygon points="18 2 22 6 12 16 8 16 8 12 18 2"/>
                </svg>
                Actualizar Estado
            </button>
        </div>
    </form>

</div>