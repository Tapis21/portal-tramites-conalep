<style>
    .anuncios-container {
        padding: 1rem;
    }

    .anuncios-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 1.5rem;
        flex-wrap: wrap;
        gap: 0.5rem;
    }

    .anuncios-title {
        font-size: 1.25rem;
        font-weight: 600;
        display: flex;
        align-items: center;
        gap: 0.5rem;
        color: #1f2937;
    }

    .anuncios-title .icon-svg {
        width: 1.5rem;
        height: 1.5rem;
        color: #2563eb;
    }

    .anuncios-grid {
        display: grid;
        grid-template-columns: 1fr;
        gap: 1rem;
    }

    .anuncio-card {
        background: #ffffff;
        border-radius: 0.75rem;
        border: 1px solid #e5e7eb;
        overflow: hidden;
        transition: all 0.2s ease;
        box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05);
    }

    .anuncio-card:hover {
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
    }

    .anuncio-card-header {
        padding: 0.75rem 1rem;
        background: linear-gradient(to right, #f9fafb, #ffffff);
        border-bottom: 1px solid #e5e7eb;
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 0.5rem;
    }

    .anuncio-card-header-left {
        display: flex;
        align-items: center;
        gap: 0.625rem;
    }

    .anuncio-icon-wrapper {
        width: 2.25rem;
        height: 2.25rem;
        background: #eff6ff;
        border-radius: 0.5rem;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }

    .anuncio-icon-wrapper .icon-svg {
        width: 1.25rem;
        height: 1.25rem;
        color: #2563eb;
    }

    .anuncio-card-title {
        font-weight: 600;
        color: #1f2937;
        font-size: 0.875rem;
    }

    .anuncio-card-date {
        font-size: 0.75rem;
        color: #9ca3af;
        display: block;
        margin-top: 0.125rem;
    }

    .anuncio-card-date .icon-svg {
        width: 0.75rem;
        height: 0.75rem;
        display: inline;
        vertical-align: middle;
    }

    .anuncio-card-header-right {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        flex-wrap: wrap;
    }

    .anuncio-badge-new {
        display: inline-flex;
        align-items: center;
        padding: 0.125rem 0.625rem;
        border-radius: 9999px;
        font-size: 0.75rem;
        font-weight: 500;
        background: #dbeafe;
        color: #1d4ed8;
    }

    .anuncio-badge-visto {
        display: inline-flex;
        align-items: center;
        padding: 0.125rem 0.625rem;
        border-radius: 9999px;
        font-size: 0.75rem;
        font-weight: 500;
        background: #f3f4f6;
        color: #6b7280;
    }

    .anuncio-admin {
        font-size: 0.75rem;
        color: #9ca3af;
        display: flex;
        align-items: center;
        gap: 0.25rem;
    }

    .anuncio-admin .icon-svg {
        width: 0.875rem;
        height: 0.875rem;
    }

    .anuncio-card-body {
        padding: 0.75rem 1rem;
    }

    .anuncio-card-body p {
        font-size: 0.875rem;
        color: #374151;
        line-height: 1.625;
        white-space: pre-wrap;
    }

    .anuncio-card-footer {
        padding: 0.625rem 1rem;
        background: #f9fafb;
        border-top: 1px solid #e5e7eb;
        display: flex;
        justify-content: flex-end;
        gap: 0.5rem;
        flex-wrap: wrap;
    }

    .anuncio-actions {
        display: flex;
        gap: 0.5rem;
    }

    .anuncio-btn-edit {
        display: inline-flex;
        align-items: center;
        gap: 0.375rem;
        padding: 0.375rem 0.75rem;
        background: #2563eb;
        color: white;
        font-size: 0.75rem;
        font-weight: 500;
        border-radius: 0.5rem;
        transition: all 0.2s ease;
        text-decoration: none;
        border: none;
        cursor: pointer;
    }

    .anuncio-btn-edit:hover {
        background: #1d4ed8;
        text-decoration: none;
        color: white;
    }

    .anuncio-btn-edit .icon-svg {
        width: 1rem;
        height: 1rem;
    }

    .anuncio-empty-state {
        padding: 3rem 1.5rem;
        text-align: center;
        background: #f9fafb;
        border-radius: 1rem;
        border: 2px dashed #d1d5db;
    }

    .anuncio-empty-icon {
        width: 5rem;
        height: 5rem;
        margin: 0 auto 1rem;
        background: #f3f4f6;
        border-radius: 9999px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .anuncio-empty-icon .icon-svg {
        width: 2.5rem;
        height: 2.5rem;
        color: #9ca3af;
    }

    .anuncio-empty-title {
        font-size: 1.125rem;
        font-weight: 600;
        color: #374151;
    }

    .anuncio-empty-description {
        font-size: 0.875rem;
        color: #9ca3af;
        margin-top: 0.25rem;
    }

    .anuncio-btn-create {
        display: inline-flex;
        align-items: center;
        gap: 0.375rem;
        padding: 0.375rem 0.75rem;
        background: #2563eb;
        color: white;
        font-size: 0.75rem;
        font-weight: 500;
        border-radius: 0.5rem;
        transition: all 0.2s ease;
        text-decoration: none;
        border: none;
        cursor: pointer;
        box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05);
    }

    .anuncio-btn-create:hover {
        background: #1d4ed8;
        text-decoration: none;
        color: white;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
    }

    .anuncio-btn-create .icon-svg {
        width: 1rem;
        height: 1rem;
    }

    /* ===== DARK MODE ===== */
    .dark .anuncio-card {
        background: #1f2937;
        border-color: #374151;
    }

    .dark .anuncio-card-header {
        background: linear-gradient(to right, #1f2937, #111827);
        border-color: #374151;
    }

    .dark .anuncio-card-title {
        color: #f3f4f6;
    }

    .dark .anuncio-card-body p {
        color: #d1d5db;
    }

    .dark .anuncio-card-footer {
        background: #1f2937;
        border-color: #374151;
    }

    .dark .anuncio-icon-wrapper {
        background: #1e3a5f;
    }

    .dark .anuncio-icon-wrapper .icon-svg {
        color: #60a5fa;
    }

    .dark .anuncio-badge-new {
        background: #1e3a5f;
        color: #60a5fa;
    }

    .dark .anuncio-badge-visto {
        background: #374151;
        color: #9ca3af;
    }

    .dark .anuncio-admin {
        color: #6b7280;
    }

    .dark .anuncio-card-date {
        color: #6b7280;
    }

    .dark .anuncios-title {
        color: #f3f4f6;
    }

    .dark .anuncio-empty-state {
        background: #1f2937;
        border-color: #374151;
    }

    .dark .anuncio-empty-title {
        color: #f3f4f6;
    }

    .dark .anuncio-empty-description {
        color: #6b7280;
    }

    .dark .anuncio-empty-icon {
        background: #374151;
    }

    .dark .anuncio-empty-icon .icon-svg {
        color: #6b7280;
    }

    /* ===== RESPONSIVE ===== */
    @media (min-width: 768px) {
        .anuncios-grid {
            grid-template-columns: repeat(2, 1fr);
        }
    }

    @media (max-width: 640px) {
        .anuncio-card-header {
            flex-direction: column;
            align-items: flex-start;
        }
        .anuncio-card-header-right {
            width: 100%;
            justify-content: flex-start;
        }
        .anuncio-card-footer {
            flex-wrap: wrap;
        }
    }
</style>

<div class="anuncios-container">
    {{-- ===== HEADER ===== --}}
    <div class="anuncios-header">
        <h2 class="anuncios-title">
            <svg class="icon-svg" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"/>
            </svg>
            Anuncios
        </h2>
        <div>
            <a href="{{ route('filament.admin.resources.anuncios.create') }}" class="anuncio-btn-create">
                <svg class="icon-svg" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Nuevo anuncio
            </a>
        </div>
    </div>

    {{-- ===== CARDS ===== --}}
    @if($anuncios->count() > 0)
        <div class="anuncios-grid">
            @foreach($anuncios as $anuncio)
                <div class="anuncio-card">
                    {{-- Header --}}
                    <div class="anuncio-card-header">
                        <div class="anuncio-card-header-left">
                            <div class="anuncio-icon-wrapper">
                                <svg class="icon-svg" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"/>
                                </svg>
                            </div>
                            <div>
                                <span class="anuncio-card-title">Anuncio</span>
                                <span class="anuncio-card-date">
                                    <svg class="icon-svg" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                    {{ $anuncio->created_at->format('d/m/Y H:i') }}
                                </span>
                            </div>
                        </div>
                        <div class="anuncio-card-header-right">
                            @if(!$anuncio->vistoPor($user))
                                <span class="anuncio-badge-new">Nuevo</span>
                            @else
                                <span class="anuncio-badge-visto">Visto</span>
                            @endif
                            <span class="anuncio-admin">
                                <svg class="icon-svg" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                </svg>
                                {{ $anuncio->admin->name ?? 'Admin' }}
                            </span>
                        </div>
                    </div>

                    {{-- Body --}}
                    <div class="anuncio-card-body">
                        <p>{{ $anuncio->contenido }}</p>
                    </div>

                    {{-- Footer: Solo Editar --}}
                    <div class="anuncio-card-footer">
                        <div class="anuncio-actions">
                            <a href="{{ route('filament.admin.resources.anuncios.edit', $anuncio) }}" class="anuncio-btn-edit">
                                <svg class="icon-svg" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
                                </svg>
                                Editar
                            </a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="anuncio-empty-state">
            <div class="anuncio-empty-icon">
                <svg class="icon-svg" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"/>
                </svg>
            </div>
            <h3 class="anuncio-empty-title">No hay anuncios</h3>
            <p class="anuncio-empty-description">Crea tu primer anuncio haciendo clic en "Nuevo anuncio".</p>
        </div>
    @endif
</div>