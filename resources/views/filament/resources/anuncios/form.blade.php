<style>
    .anuncio-form-container {
        padding: 1rem;
    }

    .anuncio-form-card {
        background: #ffffff;
        border-radius: 0.75rem;
        border: 1px solid #e5e7eb;
        overflow: hidden;
        box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05);
        transition: all 0.2s ease;
    }

    .anuncio-form-card:hover {
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
    }

    .anuncio-form-header {
        padding: 0.75rem 1.5rem;
        background: linear-gradient(to right, #f9fafb, #ffffff);
        border-bottom: 1px solid #e5e7eb;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .anuncio-form-header .icon-svg {
        width: 1.25rem;
        height: 1.25rem;
        color: #2563eb;
    }

    .anuncio-form-header h2 {
        font-size: 1.125rem;
        font-weight: 600;
        color: #1f2937;
    }

    .anuncio-form-body {
        padding: 1.5rem;
    }

    .anuncio-form-body label {
        display: block;
        font-size: 0.875rem;
        font-weight: 500;
        color: #374151;
        margin-bottom: 0.25rem;
    }

    .anuncio-form-body .label-required {
        color: #ef4444;
    }

    .anuncio-form-body textarea {
        width: 100%;
        border-radius: 0.5rem;
        border: 1px solid #d1d5db;
        padding: 0.75rem;
        font-size: 1rem;
        line-height: 1.625;
        transition: all 0.2s ease;
        min-height: 180px;
        resize: vertical;
        font-family: inherit;
    }

    .anuncio-form-body textarea:focus {
        border-color: #2563eb;
        box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
        outline: none;
    }

    .anuncio-form-body .helper-row {
        display: flex;
        justify-content: space-between;
        margin-top: 0.25rem;
        font-size: 0.75rem;
    }

    .anuncio-form-body .helper-text {
        color: #9ca3af;
    }

    .anuncio-form-body .char-count {
        color: #6b7280;
        transition: color 0.2s ease;
    }

    .anuncio-form-body .char-count.warning {
        color: #f59e0b;
    }

    .anuncio-form-body .char-count.danger {
        color: #ef4444;
    }

    .anuncio-form-body .error-text {
        margin-top: 0.25rem;
        font-size: 0.75rem;
        color: #ef4444;
    }

    .anuncio-form-footer {
        padding: 1rem 1.5rem;
        background: #f9fafb;
        border-top: 1px solid #e5e7eb;
        display: flex;
        justify-content: flex-end;
        gap: 0.625rem;
        flex-wrap: wrap;
    }

    .anuncio-form-footer .btn-cancel {
        padding: 0.5rem 1rem;
        background: #e5e7eb;
        color: #374151;
        font-size: 0.875rem;
        font-weight: 500;
        border-radius: 0.75rem;
        transition: all 0.2s ease;
        text-decoration: none;
        border: none;
        cursor: pointer;
    }

    .anuncio-form-footer .btn-cancel:hover {
        background: #d1d5db;
    }

    .anuncio-form-footer .btn-submit {
        padding: 0.5rem 1rem;
        background: #2563eb;
        color: white;
        font-size: 0.875rem;
        font-weight: 500;
        border-radius: 0.75rem;
        transition: all 0.2s ease;
        text-decoration: none;
        border: none;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 0.375rem;
    }

    .anuncio-form-footer .btn-submit:hover {
        background: #1d4ed8;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
    }

    .anuncio-form-footer .btn-submit .icon-svg {
        width: 1rem;
        height: 1rem;
    }

    /* ===== DARK MODE ===== */
    .dark .anuncio-form-card {
        background: #1f2937;
        border-color: #374151;
    }

    .dark .anuncio-form-header {
        background: linear-gradient(to right, #1f2937, #111827);
        border-color: #374151;
    }

    .dark .anuncio-form-header h2 {
        color: #f3f4f6;
    }

    .dark .anuncio-form-header .icon-svg {
        color: #60a5fa;
    }

    .dark .anuncio-form-footer {
        background: #1f2937;
        border-color: #374151;
    }

    .dark .anuncio-form-footer .btn-cancel {
        background: #374151;
        color: #d1d5db;
    }

    .dark .anuncio-form-footer .btn-cancel:hover {
        background: #4b5563;
    }

    .dark .anuncio-form-body label {
        color: #d1d5db;
    }

    .dark .anuncio-form-body textarea {
        background: #374151;
        border-color: #4b5563;
        color: #f3f4f6;
    }

    .dark .anuncio-form-body textarea:focus {
        border-color: #60a5fa;
        box-shadow: 0 0 0 3px rgba(96, 165, 250, 0.2);
    }

    .dark .anuncio-form-body .helper-text {
        color: #6b7280;
    }

    .dark .anuncio-form-body .char-count {
        color: #6b7280;
    }

    @media (max-width: 640px) {
        .anuncio-form-header {
            padding: 0.75rem 1rem;
        }
        .anuncio-form-body {
            padding: 1rem;
        }
        .anuncio-form-footer {
            flex-direction: column;
            align-items: stretch;
        }
        .anuncio-form-footer .btn-cancel,
        .anuncio-form-footer .btn-submit {
            text-align: center;
            justify-content: center;
        }
    }
</style>

<div class="anuncio-form-container">
    <div class="anuncio-form-card">
        {{-- ===== HEADER ===== --}}
        <div class="anuncio-form-header">
            <svg class="icon-svg" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"/>
            </svg>
            <h2>{{ isset($record) ? 'Editar anuncio' : 'Nuevo anuncio' }}</h2>
        </div>

        {{-- ===== BODY ===== --}}
        <div class="anuncio-form-body">
            @if(isset($record))
                {{-- 👇 EDITAR: Formulario manual con datos precargados --}}
                <form action="{{ $this->getUrl(['record' => $record]) }}" method="POST" id="anuncioForm">
                    @method('PUT')
                    @csrf
                    <div>
                        <label for="contenido">
                            Contenido del anuncio
                            <span class="label-required">*</span>
                        </label>
                        <textarea 
                            name="contenido" 
                            id="contenido" 
                            rows="6" 
                            placeholder="Escribe el anuncio que quieres publicar..."
                            maxlength="1000"
                            required
                        >{{ old('contenido', $record->contenido) }}</textarea>
                        <div class="helper-row">
                            <span class="helper-text">Máximo 1000 caracteres</span>
                            <span class="char-count" id="charCount">0 / 1000</span>
                        </div>
                        @error('contenido')
                            <p class="error-text">{{ $message }}</p>
                        @enderror
                    </div>
                </form>
            @else
                {{-- 👇 CREAR: Filament maneja el formulario --}}
                {{ $this->form }}
            @endif
        </div>

        {{-- ===== FOOTER ===== --}}
        <div class="anuncio-form-footer">
            <a href="{{ route('filament.admin.resources.anuncios.index') }}" class="btn-cancel">
                Cancelar
            </a>
            @if(isset($record))
                <button type="submit" form="anuncioForm" class="btn-submit">
                    <svg class="icon-svg" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    Actualizar anuncio
                </button>
            @else
                <button type="submit" form="form" class="btn-submit">
                    <svg class="icon-svg" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    Publicar anuncio
                </button>
            @endif
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const textarea = document.getElementById('contenido');
        const charCount = document.getElementById('charCount');

        if (textarea && charCount) {
            const updateCount = function() {
                const length = textarea.value.length;
                const maxLength = 1000;
                charCount.textContent = length + ' / ' + maxLength;

                charCount.classList.remove('warning', 'danger');

                if (length > 900) {
                    charCount.classList.add('danger');
                } else if (length > 800) {
                    charCount.classList.add('warning');
                }
            };

            textarea.addEventListener('input', updateCount);
            updateCount();
        }
    });
</script>