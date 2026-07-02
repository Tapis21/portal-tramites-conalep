{{-- resources/views/filament/modals/view-comentarios-modal.blade.php --}}
@php
    $comentarios = $record->comentarios->sortByDesc('created_at');
@endphp

<div style="padding: 8px;">

    <!-- Encabezado -->
    <div style="display: flex; align-items: center; gap: 16px; border-bottom: 2px solid #e5e7eb; padding-bottom: 16px; margin-bottom: 16px;">
        <div style="display: flex; align-items: center; gap: 12px;">
            <div style="padding: 12px; background: #dbeafe; border-radius: 12px; display: flex; align-items: center; justify-content: center;">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#2563eb" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/>
                </svg>
            </div>
            <div>
                <h3 style="font-size: 20px; font-weight: bold; color: #111827; margin: 0;">Comentarios del Documento</h3>
                <p style="font-size: 13px; color: #6b7280; margin: 0;">{{ $record->tipoDocumento->nombre }}</p>
            </div>
        </div>
    </div>

    <!-- Lista de comentarios -->
    <div style="max-height: 400px; overflow-y: auto; padding: 4px;">
        @forelse($comentarios as $comentario)
            @php
                $nombre = $comentario->user->name ?? 'Usuario';
                $esAdmin = $comentario->tipo === 'admin';
                $fecha = $comentario->created_at->format('d/m/Y H:i');
            @endphp
            <div style="padding: 16px; margin-bottom: 12px; border-radius: 10px; border: 1px solid {{ $esAdmin ? '#bfdbfe' : '#e5e7eb' }}; background: {{ $esAdmin ? '#eff6ff' : '#f9fafb' }}; box-shadow: 0 1px 2px rgba(0,0,0,0.05);">
                <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 6px;">
                    <!-- Ícono según rol -->
                    @if($esAdmin)
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#2563eb" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
                            <circle cx="12" cy="7" r="4"/>
                            <path d="M12 3a3 3 0 1 0 0 6 3 3 0 0 0 0-6z"/>
                            <path d="M12 9v4"/>
                            <path d="M10 11h4"/>
                        </svg>
                    @else
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#6b7280" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
                            <circle cx="12" cy="7" r="4"/>
                        </svg>
                    @endif
                    <span style="font-weight: 600; color: #111827; font-size: 14px;">{{ $nombre }}</span>
                    <span style="display: inline-block; padding: 2px 10px; border-radius: 9999px; font-size: 10px; font-weight: 600; background: {{ $esAdmin ? '#dbeafe' : '#f3f4f6' }}; color: {{ $esAdmin ? '#1e40af' : '#6b7280' }};">
                        {{ $esAdmin ? 'Administrador' : 'Estudiante' }}
                    </span>
                    <span style="font-size: 11px; color: #9ca3af; margin-left: auto;">{{ $fecha }}</span>
                </div>
                <p style="font-size: 14px; color: #374151; margin: 0; padding-left: 4px; border-left: 2px solid {{ $esAdmin ? '#bfdbfe' : '#e5e7eb' }}; padding-left: 12px;">
                    {{ $comentario->contenido }}
                </p>
            </div>
        @empty
            <div style="text-align: center; padding: 40px 20px; color: #6b7280;">
                <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="#d1d5db" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" style="margin: 0 auto 12px;">
                    <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/>
                </svg>
                <p style="font-size: 14px; font-weight: 500;">No hay comentarios</p>
                <p style="font-size: 13px; color: #9ca3af;">Este documento aún no tiene comentarios.</p>
            </div>
        @endforelse
    </div>

</div>