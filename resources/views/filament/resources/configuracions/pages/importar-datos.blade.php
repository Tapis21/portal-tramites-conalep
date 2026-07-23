{{-- resources/views/filament/resources/configuracions/pages/importar-datos.blade.php --}}
<x-filament-panels::page>
    <style>
        /* ========================================== */
        /* CONTENEDOR PRINCIPAL */
        /* ========================================== */
        .importar-container {
            max-width: 1024px;
            margin: 0 auto;
        }

        .importar-container .form-wrapper {
            background: #ffffff;
            border-radius: 16px;
            border: 1px solid #e5e7eb;
            padding: 1.5rem;
            box-shadow: 0 1px 2px rgba(0,0,0,0.05);
        }
        .dark .importar-container .form-wrapper {
            background: #1f2937;
            border-color: #374151;
        }

        @media (min-width: 640px) {
            .importar-container .form-wrapper {
                padding: 2rem;
            }
        }

        /* ========================================== */
        /* TARJETAS DE ESTADÍSTICAS */
        /* ========================================== */
        .importar-container .stats-grid {
            display: grid;
            grid-template-columns: 1fr;
            gap: 1rem;
            margin-bottom: 1.5rem;
        }

        @media (min-width: 768px) {
            .importar-container .stats-grid {
                grid-template-columns: 1fr 1fr 1fr;
            }
        }

        .importar-container .stat-card {
            display: flex;
            align-items: flex-start;
            gap: 0.75rem;
            padding: 1rem;
            border-radius: 16px;
            border: 1px solid #e5e7eb;
            background: #f9fafb;
            transition: all 0.2s;
        }
        .dark .importar-container .stat-card {
            background: #1f2937;
            border-color: #374151;
        }
        .importar-container .stat-card:hover {
            box-shadow: 0 4px 12px rgba(0,0,0,0.08);
            transform: translateY(-2px);
        }

        .importar-container .stat-card .icon-wrapper {
            flex-shrink: 0;
            width: 40px;
            height: 40px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .importar-container .stat-card .icon-wrapper.blue {
            background: rgba(59, 130, 246, 0.1);
        }
        .dark .importar-container .stat-card .icon-wrapper.blue {
            background: rgba(59, 130, 246, 0.2);
        }
        .importar-container .stat-card .icon-wrapper.green {
            background: rgba(16, 185, 129, 0.1);
        }
        .dark .importar-container .stat-card .icon-wrapper.green {
            background: rgba(16, 185, 129, 0.2);
        }
        .importar-container .stat-card .icon-wrapper.purple {
            background: rgba(139, 92, 246, 0.1);
        }
        .dark .importar-container .stat-card .icon-wrapper.purple {
            background: rgba(139, 92, 246, 0.2);
        }

        .importar-container .stat-card .icon-wrapper svg {
            width: 20px;
            height: 20px;
        }
        .importar-container .stat-card .icon-wrapper.blue svg {
            color: #2563eb;
        }
        .dark .importar-container .stat-card .icon-wrapper.blue svg {
            color: #60a5fa;
        }
        .importar-container .stat-card .icon-wrapper.green svg {
            color: #059669;
        }
        .dark .importar-container .stat-card .icon-wrapper.green svg {
            color: #34d399;
        }
        .importar-container .stat-card .icon-wrapper.purple svg {
            color: #7c3aed;
        }
        .dark .importar-container .stat-card .icon-wrapper.purple svg {
            color: #a78bfa;
        }

        .importar-container .stat-card .stat-title {
            font-weight: 600;
            font-size: 0.875rem;
            color: #1f2937;
        }
        .dark .importar-container .stat-card .stat-title {
            color: #f3f4f6;
        }
        .importar-container .stat-card .stat-title.blue {
            color: #1e40af;
        }
        .dark .importar-container .stat-card .stat-title.blue {
            color: #93c5fd;
        }
        .importar-container .stat-card .stat-title.green {
            color: #065f46;
        }
        .dark .importar-container .stat-card .stat-title.green {
            color: #6ee7b7;
        }
        .importar-container .stat-card .stat-title.purple {
            color: #5b21b6;
        }
        .dark .importar-container .stat-card .stat-title.purple {
            color: #c4b5fd;
        }

        .importar-container .stat-card .stat-desc {
            font-size: 0.75rem;
            color: #6b7280;
            margin-top: 2px;
        }
        .dark .importar-container .stat-card .stat-desc {
            color: #9ca3af;
        }
        .importar-container .stat-card .stat-highlight {
            font-size: 0.7rem;
            margin-top: 4px;
            font-weight: 500;
        }
        .importar-container .stat-card .stat-highlight.blue {
            color: #2563eb;
        }
        .dark .importar-container .stat-card .stat-highlight.blue {
            color: #60a5fa;
        }
        .importar-container .stat-card .stat-highlight.green {
            color: #059669;
        }
        .dark .importar-container .stat-card .stat-highlight.green {
            color: #34d399;
        }
        .importar-container .stat-card .stat-highlight.purple {
            color: #7c3aed;
        }
        .dark .importar-container .stat-card .stat-highlight.purple {
            color: #a78bfa;
        }

        /* ========================================== */
        /* INSTRUCCIONES */
        /* ========================================== */
        .importar-container .instructions-grid {
            display: grid;
            grid-template-columns: 1fr;
            gap: 0.75rem;
        }

        @media (min-width: 768px) {
            .importar-container .instructions-grid {
                grid-template-columns: 1fr 1fr;
            }
        }

        .importar-container .instruction-step {
            display: flex;
            align-items: flex-start;
            gap: 0.75rem;
            padding: 0.75rem;
            border-radius: 12px;
            border: 1px solid #e5e7eb;
        }
        .dark .importar-container .instruction-step {
            border-color: #374151;
        }

        .importar-container .instruction-step .step-number {
            flex-shrink: 0;
            width: 32px;
            height: 32px;
            border-radius: 9999px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 0.75rem;
            color: #ffffff;
        }
        .importar-container .instruction-step .step-number.blue {
            background: #3b82f6;
        }
        .importar-container .instruction-step .step-number.green {
            background: #10b981;
        }
        .importar-container .instruction-step .step-number.yellow {
            background: #f59e0b;
        }
        .importar-container .instruction-step .step-number.purple {
            background: #8b5cf6;
        }

        .importar-container .instruction-step .step-title {
            font-weight: 600;
            font-size: 0.875rem;
            color: #1f2937;
        }
        .dark .importar-container .instruction-step .step-title {
            color: #f3f4f6;
        }
        .importar-container .instruction-step .step-title.blue {
            color: #1e40af;
        }
        .dark .importar-container .instruction-step .step-title.blue {
            color: #93c5fd;
        }
        .importar-container .instruction-step .step-title.green {
            color: #065f46;
        }
        .dark .importar-container .instruction-step .step-title.green {
            color: #6ee7b7;
        }
        .importar-container .instruction-step .step-title.yellow {
            color: #92400e;
        }
        .dark .importar-container .instruction-step .step-title.yellow {
            color: #fcd34d;
        }
        .importar-container .instruction-step .step-title.purple {
            color: #5b21b6;
        }
        .dark .importar-container .instruction-step .step-title.purple {
            color: #c4b5fd;
        }

        .importar-container .instruction-step .step-desc {
            font-size: 0.75rem;
            color: #6b7280;
            margin-top: 2px;
        }
        .dark .importar-container .instruction-step .step-desc {
            color: #9ca3af;
        }

        /* ========================================== */
        /* INFO TÉCNICA */
        /* ========================================== */
        .importar-container .tech-grid {
            display: grid;
            grid-template-columns: 1fr;
            gap: 1rem;
        }

        @media (min-width: 768px) {
            .importar-container .tech-grid {
                grid-template-columns: 1fr 1fr;
            }
        }

        .importar-container .tech-card {
            padding: 1.25rem;
            border-radius: 12px;
            border: 1px solid #e5e7eb;
            background: #f9fafb;
        }
        .dark .importar-container .tech-card {
            background: #1f2937;
            border-color: #374151;
        }
        .importar-container .tech-card.blue {
            border-color: #bfdbfe;
        }
        .dark .importar-container .tech-card.blue {
            border-color: #1e3a5f;
        }
        .importar-container .tech-card.green {
            border-color: #bbf7d0;
        }
        .dark .importar-container .tech-card.green {
            border-color: #1e3a2f;
        }

        .importar-container .tech-card .tech-header {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            margin-bottom: 0.75rem;
        }
        .importar-container .tech-card .tech-header svg {
            width: 20px;
            height: 20px;
        }
        .importar-container .tech-card.blue .tech-header svg {
            color: #2563eb;
        }
        .dark .importar-container .tech-card.blue .tech-header svg {
            color: #60a5fa;
        }
        .importar-container .tech-card.green .tech-header svg {
            color: #059669;
        }
        .dark .importar-container .tech-card.green .tech-header svg {
            color: #34d399;
        }

        .importar-container .tech-card .tech-title {
            font-weight: 700;
            font-size: 0.875rem;
        }
        .importar-container .tech-card.blue .tech-title {
            color: #1e40af;
        }
        .dark .importar-container .tech-card.blue .tech-title {
            color: #93c5fd;
        }
        .importar-container .tech-card.green .tech-title {
            color: #065f46;
        }
        .dark .importar-container .tech-card.green .tech-title {
            color: #6ee7b7;
        }

        .importar-container .tech-card .tech-subtitle {
            font-weight: 600;
            font-size: 0.7rem;
            color: #6b7280;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }
        .dark .importar-container .tech-card .tech-subtitle {
            color: #9ca3af;
        }

        .importar-container .tech-card ul {
            list-style: none;
            padding: 0;
            margin: 0.25rem 0 0 0;
        }
        .importar-container .tech-card ul li {
            font-size: 0.75rem;
            color: #4b5563;
            padding: 2px 0;
        }
        .dark .importar-container .tech-card ul li {
            color: #d1d5db;
        }
        .importar-container .tech-card ul li strong {
            color: #1f2937;
        }
        .dark .importar-container .tech-card ul li strong {
            color: #f3f4f6;
        }

        .importar-container .tech-card .tech-tip {
            margin-top: 0.75rem;
            padding: 0.75rem;
            border-radius: 8px;
            font-size: 0.75rem;
        }
        .importar-container .tech-card.blue .tech-tip {
            background: rgba(59, 130, 246, 0.08);
            color: #1e40af;
        }
        .dark .importar-container .tech-card.blue .tech-tip {
            background: rgba(59, 130, 246, 0.15);
            color: #93c5fd;
        }
        .importar-container .tech-card.green .tech-tip {
            background: rgba(16, 185, 129, 0.08);
            color: #065f46;
        }
        .dark .importar-container .tech-card.green .tech-tip {
            background: rgba(16, 185, 129, 0.15);
            color: #6ee7b7;
        }
        .importar-container .tech-card .tech-tip span {
            margin-right: 4px;
        }

        /* ================================================================ */
        /* 🆕 ESTILOS PARA LAS SECCIONES */
        /* ================================================================ */
        .section-tipo,
        .section-actions,
        .section-upload {
            border-radius: 16px !important;
            padding: 1.5rem !important;
            margin-bottom: 1rem !important;
        }

        .section-tipo {
            background: #f9fafb !important;
            border: 1px solid #bfdbfe !important;
        }
        .dark .section-tipo {
            background: #1f2937 !important;
            border-color: #1e3a5f !important;
        }

        .section-actions {
            background: #f9fafb !important;
            border: 1px solid #fde68a !important;
        }
        .dark .section-actions {
            background: #1f2937 !important;
            border-color: #78350f !important;
        }

        .section-upload {
            background: #f9fafb !important;
            border: 1px solid #bbf7d0 !important;
        }
        .dark .section-upload {
            background: #1f2937 !important;
            border-color: #1e3a2f !important;
        }

        /* ================================================================ */
        /* 🆕 ESTILOS PARA EL SELECT */
        /* ================================================================ */
        .importar-container .fi-select {
            border-radius: 12px !important;
            border: 2px solid #e5e7eb !important;
            transition: all 0.2s ease !important;
        }
        .dark .importar-container .fi-select {
            border-color: #374151 !important;
            background-color: #1f2937 !important;
        }
        .importar-container .fi-select:focus {
            border-color: #059669 !important;
            box-shadow: 0 0 0 4px rgba(5, 150, 105, 0.15) !important;
        }
        .dark .importar-container .fi-select:focus {
            border-color: #34d399 !important;
            box-shadow: 0 0 0 4px rgba(52, 211, 153, 0.2) !important;
        }

        /* ================================================================ */
        /* 🆕 ESTILOS PARA EL FILE UPLOAD */
        /* ================================================================ */
        .importar-container .fi-file-upload {
            border-radius: 12px !important;
            border: 2px dashed #d1d5db !important;
            padding: 2rem !important;
            background-color: #f9fafb !important;
            transition: all 0.3s ease !important;
        }
        .dark .importar-container .fi-file-upload {
            border-color: #374151 !important;
            background-color: #1f2937 !important;
        }
        .importar-container .fi-file-upload:hover {
            border-color: #059669 !important;
            background-color: #ecfdf5 !important;
        }
        .dark .importar-container .fi-file-upload:hover {
            border-color: #34d399 !important;
            background-color: #064e3b !important;
        }

        /* ================================================================ */
        /* 🆕 ESTILOS PARA LOS BOTONES */
        /* ================================================================ */
        .importar-container .fi-btn {
            border-radius: 12px !important;
            font-weight: 600 !important;
            padding: 0.65rem 1.5rem !important;
            transition: all 0.25s ease !important;
        }
        .importar-container .fi-btn:hover {
            transform: translateY(-2px) !important;
            box-shadow: 0 8px 20px rgba(0,0,0,0.12) !important;
        }
        .importar-container .fi-btn:active {
            transform: translateY(0px) !important;
        }
    </style>

    <div class="importar-container">
        <div class="form-wrapper">
            <form wire:submit="importar" id="importar-form" class="space-y-6">
                {{ $this->form }}
            </form>
        </div>
    </div>
</x-filament-panels::page>