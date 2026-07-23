{{-- resources/views/filament/modals/view-servicio-social.blade.php --}}
<x-filament-panels::page>
    <style>
        /* ================================================================ */
        /* 📋 ESTILOS PARA LA VISTA DE SERVICIO SOCIAL */
        /* ================================================================ */

        /* 🔥 CONTENEDOR PRINCIPAL */
        .vs-wrapper {
            padding: 0.5rem;
        }

        /* ================================================================ */
        /* 📦 SECCIONES DEL INFOLIST (TARJETAS) */
        /* ================================================================ */
        .vs-wrapper .info-section {
            background: #ffffff !important;
            border-radius: 16px !important;
            border: 1px solid #e5e7eb !important;
            padding: 1.25rem !important;
            margin-bottom: 1rem !important;
            box-shadow: 0 1px 3px rgba(0,0,0,0.05) !important;
            transition: all 0.2s ease !important;
        }

        .dark .vs-wrapper .info-section {
            background: #1f2937 !important;
            border-color: #374151 !important;
        }

        .vs-wrapper .info-section:hover {
            box-shadow: 0 4px 12px rgba(0,0,0,0.08) !important;
        }

        /* 🔥 ESTUDIANTE */
        .vs-wrapper .info-section-student {
            border-left: 4px solid #3b82f6 !important;
        }
        .dark .vs-wrapper .info-section-student {
            border-left-color: #60a5fa !important;
        }

        /* 🔥 EMPRESA */
        .vs-wrapper .info-section-company {
            border-left: 4px solid #10b981 !important;
        }
        .dark .vs-wrapper .info-section-company {
            border-left-color: #34d399 !important;
        }

        /* 🔥 SIN SOLICITUD */
        .vs-wrapper .info-section-empty {
            border-left: 4px solid #f59e0b !important;
            background: #fffbeb !important;
        }
        .dark .vs-wrapper .info-section-empty {
            border-left-color: #fbbf24 !important;
            background: #451a03 !important;
        }

        /* 🔥 FECHAS */
        .vs-wrapper .info-section-dates {
            border-left: 4px solid #8b5cf6 !important;
        }
        .dark .vs-wrapper .info-section-dates {
            border-left-color: #a78bfa !important;
        }

        /* 🔥 CONTACTO */
        .vs-wrapper .info-section-contact {
            border-left: 4px solid #f59e0b !important;
        }
        .dark .vs-wrapper .info-section-contact {
            border-left-color: #fbbf24 !important;
        }

        /* 🔥 ADICIONAL */
        .vs-wrapper .info-section-additional {
            border-left: 4px solid #ec4899 !important;
        }
        .dark .vs-wrapper .info-section-additional {
            border-left-color: #f472b6 !important;
        }

        /* 🔥 TÍTULOS DE SECCIONES */
        .vs-wrapper .info-section .fi-section-header {
            font-weight: 700 !important;
            color: #0f172a !important;
            font-size: 0.9rem !important;
            margin-bottom: 0.75rem !important;
        }
        .dark .vs-wrapper .info-section .fi-section-header {
            color: #e2e8f0 !important;
        }

        /* 🔥 TEXTENTRY (valores) */
        .vs-wrapper .fi-text-entry {
            padding: 0.25rem 0 !important;
        }

        .vs-wrapper .fi-text-entry .fi-text-entry-label {
            font-size: 0.7rem !important;
            font-weight: 600 !important;
            color: #64748b !important;
            text-transform: uppercase !important;
            letter-spacing: 0.05em !important;
        }
        .dark .vs-wrapper .fi-text-entry .fi-text-entry-label {
            color: #94a3b8 !important;
        }

        .vs-wrapper .fi-text-entry .fi-text-entry-value {
            font-size: 0.9rem !important;
            font-weight: 500 !important;
            color: #1e293b !important;
        }
        .dark .vs-wrapper .fi-text-entry .fi-text-entry-value {
            color: #e2e8f0 !important;
        }

        /* 🔥 BADGES DE ESTATUS */
        .vs-wrapper .fi-badge {
            padding: 0.25rem 0.9rem !important;
            border-radius: 9999px !important;
            font-size: 0.7rem !important;
            font-weight: 600 !important;
            text-transform: uppercase !important;
            letter-spacing: 0.04em !important;
            box-shadow: 0 1px 3px rgba(0,0,0,0.06) !important;
        }

        /* ================================================================ */
        /* 📋 TABLA DE DOCUMENTOS */
        /* ================================================================ */
        .vs-wrapper .documents-table .fi-ta-table {
            border-radius: 12px !important;
            border: 1px solid #e5e7eb !important;
            overflow: hidden !important;
            box-shadow: 0 1px 3px rgba(0,0,0,0.05) !important;
            background: #ffffff !important;
        }

        .dark .vs-wrapper .documents-table .fi-ta-table {
            border-color: #374151 !important;
            background: #1f2937 !important;
        }

        /* 🔥 ENCABEZADOS DE LA TABLA */
        .vs-wrapper .documents-table .fi-ta-header-cell {
            background: #f1f5f9 !important;
            font-weight: 700 !important;
            color: #0f172a !important;
            padding: 0.75rem 1.25rem !important;
            border-bottom: 2px solid #e2e8f0 !important;
            font-size: 0.7rem !important;
            text-transform: uppercase !important;
            letter-spacing: 0.08em !important;
        }

        .dark .vs-wrapper .documents-table .fi-ta-header-cell {
            background: #1e293b !important;
            color: #e2e8f0 !important;
            border-bottom-color: #334155 !important;
        }

        /* 🔥 FILAS */
        .vs-wrapper .documents-table .fi-ta-row {
            transition: all 0.2s ease !important;
            border-bottom: 1px solid #f1f5f9 !important;
        }

        .dark .vs-wrapper .documents-table .fi-ta-row {
            border-bottom-color: #334155 !important;
        }

        .vs-wrapper .documents-table .fi-ta-row:hover {
            background-color: #f0fdf4 !important;
        }

        .dark .vs-wrapper .documents-table .fi-ta-row:hover {
            background-color: #064e3b !important;
        }

        /* 🔥 CELDAS */
        .vs-wrapper .documents-table .fi-ta-cell {
            padding: 0.75rem 1.25rem !important;
            font-size: 0.875rem !important;
            color: #1e293b !important;
            vertical-align: middle !important;
        }

        .dark .vs-wrapper .documents-table .fi-ta-cell {
            color: #e2e8f0 !important;
        }

        /* 🔥 ACCIONES DE LA TABLA */
        .vs-wrapper .documents-table .fi-ta-actions .fi-btn {
            border-radius: 8px !important;
            padding: 0.25rem 0.75rem !important;
            font-size: 0.75rem !important;
            font-weight: 500 !important;
            transition: all 0.25s ease !important;
        }

        .vs-wrapper .documents-table .fi-ta-actions .fi-btn:hover {
            transform: translateY(-2px) !important;
            box-shadow: 0 8px 20px rgba(0,0,0,0.12) !important;
        }

        .dark .vs-wrapper .documents-table .fi-ta-actions .fi-btn:hover {
            box-shadow: 0 8px 20px rgba(0,0,0,0.35) !important;
        }

        /* 🔥 ACCIONES CON CLASES ESPECÍFICAS */
        .vs-wrapper .doc-action {
            border-radius: 8px !important;
            padding: 0.25rem 0.75rem !important;
            font-weight: 500 !important;
            transition: all 0.25s ease !important;
        }

        .vs-wrapper .doc-action:hover {
            transform: translateY(-2px) !important;
            box-shadow: 0 8px 20px rgba(0,0,0,0.12) !important;
        }

        .dark .vs-wrapper .doc-action:hover {
            box-shadow: 0 8px 20px rgba(0,0,0,0.35) !important;
        }

        .vs-wrapper .view-pdf {
            background: #3b82f6 !important;
            color: white !important;
            border: none !important;
        }
        .vs-wrapper .view-pdf:hover {
            background: #2563eb !important;
        }

        .vs-wrapper .view-comments {
            background: #6b7280 !important;
            color: white !important;
            border: none !important;
        }
        .vs-wrapper .view-comments:hover {
            background: #4b5563 !important;
        }

        .vs-wrapper .add-comment {
            background: #10b981 !important;
            color: white !important;
            border: none !important;
        }
        .vs-wrapper .add-comment:hover {
            background: #059669 !important;
        }

        .vs-wrapper .change-status {
            background: #f59e0b !important;
            color: white !important;
            border: none !important;
        }
        .vs-wrapper .change-status:hover {
            background: #d97706 !important;
        }

        /* ================================================================ */
        /* 🔥 CONTENEDOR DEL INFOLIST Y TABLA (MEJORADO) */
        /* ================================================================ */
        .vs-wrapper .infolist-container {
            background: #ffffff !important;
            border-radius: 16px !important;
            border: 1px solid #e5e7eb !important;
            padding: 1rem !important;
            box-shadow: 0 1px 3px rgba(0,0,0,0.05) !important;
        }

        .dark .vs-wrapper .infolist-container {
            background: #1f2937 !important;
            border-color: #374151 !important;
        }

        /* 🔥 CONTENEDOR DE LA TABLA (más separado y con mejor color) */
        .vs-wrapper .table-container {
            background: linear-gradient(135deg, #f8fafc, #f1f5f9) !important;
            border-radius: 16px !important;
            border: 1px solid #e5e7eb !important;
            padding: 1.5rem !important;
            margin-top: 2rem !important;
            box-shadow: 0 4px 12px rgba(0,0,0,0.04) !important;
            transition: all 0.2s ease !important;
        }

        .dark .vs-wrapper .table-container {
            background: linear-gradient(135deg, #1e293b, #0f172a) !important;
            border-color: #334155 !important;
            box-shadow: 0 4px 12px rgba(0,0,0,0.2) !important;
        }

        /* 🔥 ENCABEZADO DE LA TABLA (más prominente) */
        .vs-wrapper .table-header {
            padding: 0.5rem 0 1.25rem 0 !important;
            font-size: 1.15rem !important;
            font-weight: 700 !important;
            color: #0f172a !important;
            border-bottom: 2px solid #e2e8f0 !important;
            margin-bottom: 1.25rem !important;
            display: flex !important;
            align-items: center !important;
            gap: 0.5rem !important;
        }

        .dark .vs-wrapper .table-header {
            color: #e2e8f0 !important;
            border-bottom-color: #334155 !important;
        }

        /* ================================================================ */
        /* 📱 RESPONSIVE */
        /* ================================================================ */
        @media (max-width: 640px) {
            .vs-wrapper .info-section {
                padding: 0.75rem !important;
            }
            .vs-wrapper .documents-table .fi-ta-header-cell,
            .vs-wrapper .documents-table .fi-ta-cell {
                padding: 0.5rem 0.75rem !important;
                font-size: 0.75rem !important;
            }
            .vs-wrapper .documents-table .fi-ta-actions .fi-btn,
            .vs-wrapper .doc-action {
                padding: 0.15rem 0.5rem !important;
                font-size: 0.65rem !important;
            }
            .vs-wrapper .table-container {
                padding: 1rem !important;
                margin-top: 1.5rem !important;
            }
        }
    </style>

    <div class="vs-wrapper">
        {{-- Infolist --}}
        <div class="space-y-4">
            <div class="infolist-container">
                {{ $this->infolist }}
            </div>
        </div>

        {{-- Tabla de Documentos --}}
        <div class="mt-6">
            <div class="table-container">
                <div class="table-header">
                    📄 Documentos Subidos
                </div>
                <div>
                    {{ $this->table }}
                </div>
            </div>
        </div>
    </div>
</x-filament-panels::page>