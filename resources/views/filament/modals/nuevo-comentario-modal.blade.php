{{-- resources/views/filament/modals/nuevo-comentario-modal.blade.php --}}
@php
    $user = auth()->user();
    $esAdmin = $user->role === 'admin';
@endphp

<div style="padding: 8px;">

    <!-- Encabezado -->
    <div style="display: flex; align-items: center; gap: 16px; border-bottom: 2px solid #e5e7eb; padding-bottom: 16px; margin-bottom: 16px;">
        <div style="display: flex; align-items: center; gap: 12px;">
            <div style="padding: 12px; background: #dcfce7; border-radius: 12px; display: flex; align-items: center; justify-content: center;">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#059669" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="12" cy="12" r="10"/>
                    <line x1="12" y1="8" x2="12" y2="16"/>
                    <line x1="8" y1="12" x2="16" y2="12"/>
                </svg>
            </div>
            <div>
                <h3 style="font-size: 20px; font-weight: bold; color: #111827; margin: 0;">Nuevo Comentario</h3>
                <p style="font-size: 13px; color: #6b7280; margin: 0;">{{ $record->tipoDocumento->nombre }}</p>
            </div>
        </div>
    </div>

    <!-- Formulario con Livewire -->
    <form wire:submit.prevent="agregarComentario({{ $documentoId }})">
        <div style="margin-bottom: 16px;">
            <label for="comentario_contenido" style="display: block; font-size: 13px; font-weight: 600; color: #374151; margin-bottom: 6px;">
                Comentario
            </label>
            <textarea 
                wire:model="comentario_contenido" 
                id="comentario_contenido" 
                rows="4" 
                style="width: 100%; padding: 12px; border: 1px solid #d1d5db; border-radius: 8px; font-size: 14px; font-family: inherit; outline: none; transition: border-color 0.2s; resize: vertical;"
                placeholder="Escribe tu comentario aquí..."
                required
            ></textarea>
        </div>

        <div style="display: flex; justify-content: flex-end; gap: 8px; padding-top: 8px; border-top: 1px solid #e5e7eb;">
            <button 
                type="submit" 
                style="padding: 8px 20px; border-radius: 8px; font-size: 14px; font-weight: 500; color: white; background: #059669; border: none; cursor: pointer; display: inline-flex; align-items: center; gap: 8px; transition: all 0.2s;"
                onmouseover="this.style.backgroundColor='#047857'"
                onmouseout="this.style.backgroundColor='#059669'"
            >
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M20 14.66V20a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h5.34"/>
                    <polygon points="18 2 22 6 12 16 8 16 8 12 18 2"/>
                </svg>
                Enviar Comentario
            </button>
        </div>
    </form>

</div>