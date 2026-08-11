{{-- resources/views/filament/resources/servicio-socials/pages/list-servicio-socials.blade.php --}}
<x-filament-panels::page>
    <style>
        /* ================================================================ */
        /* 📋 ESTILOS PARA LA TABLA DE SERVICIO SOCIAL - UI/UX MEJORADA */
        /* ================================================================ */

        /* 🔥 CONTENEDOR DE LA TABLA */
        .ss-table-wrapper {
            padding: 1rem !important;
        }

        .ss-table-wrapper .fi-ta-table {
            border-radius: 16px !important;
            border: 1px solid #e5e7eb !important;
            overflow: hidden !important;
            box-shadow: 0 4px 12px rgba(0,0,0,0.06) !important;
            background: #ffffff !important;
        }

        .dark .ss-table-wrapper .fi-ta-table {
            border-color: #374151 !important;
            background: #1f2937 !important;
            box-shadow: 0 4px 12px rgba(0,0,0,0.3) !important;
        }

        /* 🔥 ENCABEZADOS */
        .ss-table-wrapper .fi-ta-header-cell {
            background: #f1f5f9 !important;
            font-weight: 700 !important;
            color: #0f172a !important;
            padding: 0.875rem 1.25rem !important;
            border-bottom: 2px solid #e2e8f0 !important;
            font-size: 0.7rem !important;
            text-transform: uppercase !important;
            letter-spacing: 0.08em !important;
        }

        .dark .ss-table-wrapper .fi-ta-header-cell {
            background: #1e293b !important;
            color: #e2e8f0 !important;
            border-bottom-color: #334155 !important;
        }

        /* 🔥 FILAS */
        .ss-table-wrapper .fi-ta-row {
            transition: all 0.2s ease !important;
            border-bottom: 1px solid #f1f5f9 !important;
        }

        .dark .ss-table-wrapper .fi-ta-row {
            border-bottom-color: #334155 !important;
        }

        .ss-table-wrapper .fi-ta-row:hover {
            background-color: #f0fdf4 !important;
            transform: scale(1.002) !important;
            box-shadow: 0 2px 8px rgba(0,0,0,0.04) !important;
        }

        .dark .ss-table-wrapper .fi-ta-row:hover {
            background-color: #064e3b !important;
            box-shadow: 0 2px 8px rgba(0,0,0,0.2) !important;
        }

        /* 🔥 FILAS ALTERNAS (zebra) */
        .ss-table-wrapper .fi-ta-row:nth-child(even) {
            background-color: #f8fafc !important;
        }

        .dark .ss-table-wrapper .fi-ta-row:nth-child(even) {
            background-color: #0f172a !important;
        }

        .ss-table-wrapper .fi-ta-row:nth-child(even):hover {
            background-color: #f0fdf4 !important;
        }

        .dark .ss-table-wrapper .fi-ta-row:nth-child(even):hover {
            background-color: #064e3b !important;
        }

        /* 🔥 CELDAS */
        .ss-table-wrapper .fi-ta-cell {
            padding: 0.875rem 1.25rem !important;
            font-size: 0.875rem !important;
            color: #1e293b !important;
            vertical-align: middle !important;
        }

        .dark .ss-table-wrapper .fi-ta-cell {
            color: #e2e8f0 !important;
        }

        /* ================================================================ */
        /* 🟡 CLASE: SIN PERIODO (resaltado suave) */
        /* ================================================================ */
        .ss-table-wrapper .sin-periodo {
            border-left: 3px solid #f59e0b !important;
            background-color: rgba(254, 243, 199, 0.15) !important;
        }

        .dark .ss-table-wrapper .sin-periodo {
            border-left-color: #fbbf24 !important;
            background-color: rgba(251, 191, 36, 0.08) !important;
        }

        .ss-table-wrapper .sin-periodo:hover {
            background-color: rgba(254, 243, 199, 0.3) !important;
        }

        .dark .ss-table-wrapper .sin-periodo:hover {
            background-color: rgba(251, 191, 36, 0.15) !important;
        }

        .ss-table-wrapper .fi-ta-row:nth-child(even) .sin-periodo {
            background-color: rgba(254, 243, 199, 0.2) !important;
        }

        .dark .ss-table-wrapper .fi-ta-row:nth-child(even) .sin-periodo {
            background-color: rgba(251, 191, 36, 0.06) !important;
        }

        .ss-table-wrapper .fi-ta-row:nth-child(even) .sin-periodo:hover {
            background-color: rgba(254, 243, 199, 0.35) !important;
        }

        .dark .ss-table-wrapper .fi-ta-row:nth-child(even) .sin-periodo:hover {
            background-color: rgba(251, 191, 36, 0.12) !important;
        }

        /* ================================================================ */
        /* 🟠 CLASE: POR VENCER (7 días o menos) */
        /* ================================================================ */
        .ss-table-wrapper .por-vencer {
            border-left: 3px solid #f97316 !important;
            background-color: rgba(254, 215, 170, 0.15) !important;
        }

        .dark .ss-table-wrapper .por-vencer {
            border-left-color: #fb923c !important;
            background-color: rgba(251, 146, 60, 0.08) !important;
        }

        .ss-table-wrapper .por-vencer:hover {
            background-color: rgba(254, 215, 170, 0.3) !important;
        }

        .dark .ss-table-wrapper .por-vencer:hover {
            background-color: rgba(251, 146, 60, 0.15) !important;
        }

        /* ================================================================ */
        /* 🔴 CLASE: VENCIDO */
        /* ================================================================ */
        .ss-table-wrapper .vencido {
            border-left: 3px solid #ef4444 !important;
            background-color: rgba(254, 202, 202, 0.15) !important;
        }

        .dark .ss-table-wrapper .vencido {
            border-left-color: #f87171 !important;
            background-color: rgba(248, 113, 113, 0.08) !important;
        }

        .ss-table-wrapper .vencido:hover {
            background-color: rgba(254, 202, 202, 0.3) !important;
        }

        .dark .ss-table-wrapper .vencido:hover {
            background-color: rgba(248, 113, 113, 0.15) !important;
        }

        /* ================================================================ */
        /* 🔥 HEADER ACTIONS (FILTRO + CREAR GENERACIÓN) */
        /* ================================================================ */
        .ss-table-wrapper .fi-header-actions {
            gap: 0.75rem !important;
            padding: 0.5rem 0 1rem 0 !important;
            flex-wrap: wrap !important;
        }

        .ss-table-wrapper .fi-header-actions .fi-btn {
            border-radius: 10px !important;
            padding: 0.6rem 1.25rem !important;
            font-weight: 600 !important;
            font-size: 0.8rem !important;
            transition: all 0.25s ease !important;
            box-shadow: 0 1px 3px rgba(0,0,0,0.06) !important;
        }

        .ss-table-wrapper .fi-header-actions .fi-btn:hover {
            transform: translateY(-2px) !important;
            box-shadow: 0 8px 20px rgba(0,0,0,0.12) !important;
        }

        .dark .ss-table-wrapper .fi-header-actions .fi-btn:hover {
            box-shadow: 0 8px 20px rgba(0,0,0,0.3) !important;
        }

        /* 🔥 BOTÓN ESPECÍFICO: FILTRAR */
        .ss-table-wrapper .filter-action {
            background: linear-gradient(135deg, #10b981, #059669) !important;
            color: white !important;
            border: none !important;
        }

        .ss-table-wrapper .filter-action:hover {
            background: linear-gradient(135deg, #059669, #047857) !important;
            color: white !important;
        }

        .dark .ss-table-wrapper .filter-action {
            background: linear-gradient(135deg, #059669, #047857) !important;
        }

        .dark .ss-table-wrapper .filter-action:hover {
            background: linear-gradient(135deg, #047857, #065f46) !important;
        }

        /* 🔥 BOTÓN ESPECÍFICO: CREAR GENERACIÓN */
        .ss-table-wrapper .create-period-btn {
            background: linear-gradient(135deg, #3b82f6, #2563eb) !important;
            color: white !important;
            border: none !important;
        }

        .ss-table-wrapper .create-period-btn:hover {
            background: linear-gradient(135deg, #2563eb, #1d4ed8) !important;
            color: white !important;
        }

        .dark .ss-table-wrapper .create-period-btn {
            background: linear-gradient(135deg, #2563eb, #1d4ed8) !important;
        }

        .dark .ss-table-wrapper .create-period-btn:hover {
            background: linear-gradient(135deg, #1d4ed8, #1e40af) !important;
        }

        /* 🔥 BOTÓN ESPECÍFICO: RESETEAR FILTRO */
        .ss-table-wrapper .reset-filter-btn {
            background: linear-gradient(135deg, #ef4444, #dc2626) !important;
            color: white !important;
            border: none !important;
        }

        .ss-table-wrapper .reset-filter-btn:hover {
            background: linear-gradient(135deg, #dc2626, #b91c1c) !important;
            color: white !important;
        }

        .dark .ss-table-wrapper .reset-filter-btn {
            background: linear-gradient(135deg, #dc2626, #b91c1c) !important;
        }

        .dark .ss-table-wrapper .reset-filter-btn:hover {
            background: linear-gradient(135deg, #b91c1c, #991b1b) !important;
        }

        /* ================================================================ */
        /* 🔥 MODAL DE FILTRO */
        /* ================================================================ */
        .ss-table-wrapper .fi-modal {
            border-radius: 16px !important;
            overflow: hidden !important;
            box-shadow: 0 20px 60px rgba(0,0,0,0.15) !important;
        }

        .dark .ss-table-wrapper .fi-modal {
            box-shadow: 0 20px 60px rgba(0,0,0,0.4) !important;
        }

        .ss-table-wrapper .fi-modal-header {
            background: #f1f5f9 !important;
            padding: 1.25rem 1.5rem !important;
            border-bottom: 2px solid #e2e8f0 !important;
        }

        .dark .ss-table-wrapper .fi-modal-header {
            background: #1e293b !important;
            border-bottom-color: #334155 !important;
        }

        .ss-table-wrapper .fi-modal-header .fi-modal-heading {
            font-weight: 700 !important;
            color: #0f172a !important;
            font-size: 1.1rem !important;
        }

        .dark .ss-table-wrapper .fi-modal-header .fi-modal-heading {
            color: #e2e8f0 !important;
        }

        .ss-table-wrapper .fi-modal-body {
            padding: 1.5rem !important;
            background: #ffffff !important;
        }

        .dark .ss-table-wrapper .fi-modal-body {
            background: #1f2937 !important;
        }

        /* 🔥 SELECT CON COLORES */
        .ss-table-wrapper .filter-select-with-colors .fi-select {
            border-radius: 10px !important;
            border: 2px solid #e2e8f0 !important;
            padding: 0.5rem 1rem !important;
            transition: all 0.2s ease !important;
        }

        .dark .ss-table-wrapper .filter-select-with-colors .fi-select {
            border-color: #475569 !important;
            background: #1e293b !important;
            color: #e2e8f0 !important;
        }

        .ss-table-wrapper .filter-select-with-colors .fi-select:focus {
            border-color: #10b981 !important;
            box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.15) !important;
        }

        .dark .ss-table-wrapper .filter-select-with-colors .fi-select:focus {
            border-color: #34d399 !important;
            box-shadow: 0 0 0 3px rgba(52, 211, 153, 0.2) !important;
        }

        /* 🔥 TOGGLE */
        .ss-table-wrapper .filter-toggle .fi-toggle {
            border-radius: 8px !important;
            transition: all 0.2s ease !important;
        }

        .ss-table-wrapper .filter-toggle .fi-toggle.is-checked {
            background-color: #10b981 !important;
        }

        .dark .ss-table-wrapper .filter-toggle .fi-toggle.is-checked {
            background-color: #34d399 !important;
        }

        .ss-table-wrapper .fi-modal-footer {
            background: #f8fafc !important;
            padding: 1rem 1.5rem !important;
            border-top: 1px solid #e2e8f0 !important;
            gap: 0.75rem !important;
        }

        .dark .ss-table-wrapper .fi-modal-footer {
            background: #0f172a !important;
            border-top-color: #334155 !important;
        }

        .ss-table-wrapper .fi-modal-footer .fi-btn {
            border-radius: 8px !important;
            font-weight: 600 !important;
            padding: 0.5rem 1.25rem !important;
            transition: all 0.2s ease !important;
        }

        .ss-table-wrapper .fi-modal-footer .fi-btn:hover {
            transform: translateY(-2px) !important;
        }

        /* ================================================================ */
        /* 🔥 BOTONES DE ACCIÓN EN LA TABLA */
        /* ================================================================ */
        .ss-table-wrapper .fi-ta-actions .fi-btn {
            border-radius: 8px !important;
            padding: 0.35rem 0.85rem !important;
            font-size: 0.75rem !important;
            font-weight: 500 !important;
            transition: all 0.25s ease !important;
        }

        .ss-table-wrapper .fi-ta-actions .fi-btn:hover {
            transform: translateY(-2px) !important;
            box-shadow: 0 8px 20px rgba(0,0,0,0.12) !important;
        }

        .dark .ss-table-wrapper .fi-ta-actions .fi-btn:hover {
            box-shadow: 0 8px 20px rgba(0,0,0,0.35) !important;
        }

        /* 🔥 ACCIONES CON CLASES ESPECÍFICAS */
        .ss-table-wrapper .action-btn {
            border-radius: 8px !important;
            padding: 0.35rem 0.85rem !important;
            font-weight: 500 !important;
            transition: all 0.25s ease !important;
        }

        .ss-table-wrapper .action-btn:hover {
            transform: translateY(-2px) !important;
            box-shadow: 0 8px 20px rgba(0,0,0,0.12) !important;
        }

        .dark .ss-table-wrapper .action-btn:hover {
            box-shadow: 0 8px 20px rgba(0,0,0,0.35) !important;
        }

        .ss-table-wrapper .success-btn {
            background: #10b981 !important;
            color: white !important;
            border: none !important;
        }

        .ss-table-wrapper .success-btn:hover {
            background: #059669 !important;
        }

        .ss-table-wrapper .danger-btn {
            background: #ef4444 !important;
            color: white !important;
            border: none !important;
        }

        .ss-table-wrapper .danger-btn:hover {
            background: #dc2626 !important;
        }

        .ss-table-wrapper .info-btn {
            background: #3b82f6 !important;
            color: white !important;
            border: none !important;
        }

        .ss-table-wrapper .info-btn:hover {
            background: #2563eb !important;
        }

        /* ================================================================ */
        /* 🔥 BULK ACTIONS (ELIMINAR SELECCIONADOS) */
        /* ================================================================ */
        .ss-table-wrapper .fi-bulk-actions .fi-btn {
            border-radius: 8px !important;
            padding: 0.35rem 1rem !important;
            font-weight: 500 !important;
            transition: all 0.25s ease !important;
        }

        .ss-table-wrapper .fi-bulk-actions .fi-btn:hover {
            transform: translateY(-2px) !important;
            box-shadow: 0 8px 20px rgba(0,0,0,0.12) !important;
        }

        .dark .ss-table-wrapper .fi-bulk-actions .fi-btn:hover {
            box-shadow: 0 8px 20px rgba(0,0,0,0.35) !important;
        }

        .ss-table-wrapper .bulk-action-btn {
            background: linear-gradient(135deg, #ef4444, #dc2626) !important;
            color: white !important;
            border: none !important;
        }

        .ss-table-wrapper .bulk-action-btn:hover {
            background: linear-gradient(135deg, #dc2626, #b91c1c) !important;
        }

        /* ================================================================ */
        /* 🔥 CHECKBOXES DE SELECCIÓN */
        /* ================================================================ */
        .ss-table-wrapper .fi-checkbox {
            border-radius: 6px !important;
            border: 2px solid #d1d5db !important;
            transition: all 0.2s ease !important;
        }

        .dark .ss-table-wrapper .fi-checkbox {
            border-color: #4b5563 !important;
        }

        .ss-table-wrapper .fi-checkbox.is-checked {
            background-color: #10b981 !important;
            border-color: #10b981 !important;
        }

        .dark .ss-table-wrapper .fi-checkbox.is-checked {
            background-color: #34d399 !important;
            border-color: #34d399 !important;
        }

        /* ================================================================ */
        /* 🔥 FILTROS */
        /* ================================================================ */
        .ss-table-wrapper .fi-filters {
            padding: 0.75rem 0 !important;
            gap: 0.75rem !important;
        }

        .ss-table-wrapper .fi-filters .fi-filter {
            border-radius: 10px !important;
            border: 1px solid #e2e8f0 !important;
            padding: 0.5rem 1rem !important;
            background: #ffffff !important;
            transition: all 0.2s ease !important;
        }

        .dark .ss-table-wrapper .fi-filters .fi-filter {
            border-color: #475569 !important;
            background: #1e293b !important;
            color: #e2e8f0 !important;
        }

        .ss-table-wrapper .fi-filters .fi-filter:focus-within {
            border-color: #10b981 !important;
            box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.15) !important;
        }

        .dark .ss-table-wrapper .fi-filters .fi-filter:focus-within {
            border-color: #34d399 !important;
            box-shadow: 0 0 0 3px rgba(52, 211, 153, 0.2) !important;
        }

        /* ================================================================ */
        /* 🔥 ESTADO VACÍO */
        /* ================================================================ */
        .ss-table-wrapper .fi-ta-empty-state {
            padding: 4rem 2rem !important;
        }

        .ss-table-wrapper .fi-ta-empty-state .fi-ta-empty-state-heading {
            font-size: 1.25rem !important;
            font-weight: 700 !important;
            color: #0f172a !important;
        }

        .dark .ss-table-wrapper .fi-ta-empty-state .fi-ta-empty-state-heading {
            color: #e2e8f0 !important;
        }

        .ss-table-wrapper .fi-ta-empty-state .fi-ta-empty-state-description {
            color: #64748b !important;
            font-size: 0.9rem !important;
        }

        .dark .ss-table-wrapper .fi-ta-empty-state .fi-ta-empty-state-description {
            color: #94a3b8 !important;
        }

        /* ================================================================ */
        /* 🔥 PAGINACIÓN */
        /* ================================================================ */
        .ss-table-wrapper .fi-pagination {
            padding: 1rem 0 !important;
            border-top: 1px solid #f1f5f9 !important;
        }

        .dark .ss-table-wrapper .fi-pagination {
            border-top-color: #334155 !important;
        }

        .ss-table-wrapper .fi-pagination .fi-btn {
            border-radius: 10px !important;
            transition: all 0.2s ease !important;
            font-weight: 500 !important;
        }

        .ss-table-wrapper .fi-pagination .fi-btn:hover {
            background-color: #f0fdf4 !important;
            transform: translateY(-1px) !important;
        }

        .dark .ss-table-wrapper .fi-pagination .fi-btn:hover {
            background-color: #064e3b !important;
        }

        /* 🔥 SELECT DE PAGINACIÓN */
        .ss-table-wrapper .fi-pagination select {
            border-radius: 8px !important;
            border: 1px solid #e2e8f0 !important;
            padding: 0.25rem 0.5rem !important;
        }

        .dark .ss-table-wrapper .fi-pagination select {
            border-color: #475569 !important;
            background: #1e293b !important;
            color: #e2e8f0 !important;
        }

        /* ================================================================ */
        /* 📱 RESPONSIVE */
        /* ================================================================ */
        @media (max-width: 640px) {
            .ss-table-wrapper .fi-ta-header-cell,
            .ss-table-wrapper .fi-ta-cell {
                padding: 0.5rem 0.75rem !important;
                font-size: 0.75rem !important;
            }

            .ss-table-wrapper .fi-ta-actions .fi-btn,
            .ss-table-wrapper .action-btn {
                padding: 0.15rem 0.5rem !important;
                font-size: 0.65rem !important;
            }

            .ss-table-wrapper .fi-header-actions .fi-btn {
                padding: 0.4rem 0.8rem !important;
                font-size: 0.7rem !important;
            }

            .ss-table-wrapper .fi-bulk-actions .fi-btn {
                padding: 0.15rem 0.5rem !important;
                font-size: 0.65rem !important;
            }
        }
    </style>

    {{-- 👇 CONTENIDO DE LA PÁGINA --}}
    <div class="ss-table-wrapper">
        {{ $this->table }}
    </div>
</x-filament-panels::page>