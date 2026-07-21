{{-- resources/views/filament/empresas/view-modal-ver-empresa.blade.php --}}
<x-filament-panels::page>
    <style>
        .empresa-card {
            max-width: 1024px;
            margin: 0 auto;
            padding: 0 1rem;
        }

        /* ========================================== */
        /* TARJETA DE PRESENTACIÓN */
        /* ========================================== */
        .empresa-card .header {
            background: linear-gradient(135deg, #ecfdf5, #d1fae5);
            border: 1px solid #bbf7d0;
            border-radius: 16px;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
            box-shadow: 0 1px 2px rgba(0,0,0,0.05);
        }
        .dark .empresa-card .header {
            background: linear-gradient(135deg, #064e3b, #065f46);
            border-color: #065f46;
        }

        .empresa-card .header-content {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .empresa-card .logo {
            width: 64px;
            height: 64px;
            background: linear-gradient(135deg, #10b981, #15803d);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1);
            flex-shrink: 0;
        }
        .dark .empresa-card .logo {
            background: linear-gradient(135deg, #059669, #047857);
        }

        .empresa-card .logo span {
            font-size: 2rem;
        }

        .empresa-card .nombre {
            font-size: 1.5rem;
            font-weight: 700;
            color: #1f2937;
            margin: 0;
        }
        .dark .empresa-card .nombre {
            color: #f3f4f6;
        }

        .empresa-card .badges {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            margin-top: 4px;
            flex-wrap: wrap;
        }

        .empresa-card .badge-id {
            font-size: 0.75rem;
            font-family: monospace;
            background: #f3f4f6;
            padding: 2px 8px;
            border-radius: 4px;
            color: #6b7280;
        }
        .dark .empresa-card .badge-id {
            background: #374151;
            color: #9ca3af;
        }

        .empresa-card .badge-activo {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            font-size: 0.75rem;
            background: #d1fae5;
            color: #065f46;
            padding: 4px 12px;
            border-radius: 9999px;
            font-weight: 500;
        }
        .dark .empresa-card .badge-activo {
            background: #064e3b;
            color: #34d399;
        }

        .empresa-card .badge-activo .dot {
            width: 8px;
            height: 8px;
            background: #10b981;
            border-radius: 9999px;
            display: inline-block;
        }
        .dark .empresa-card .badge-activo .dot {
            background: #34d399;
        }

        .empresa-card .badge-inactivo {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            font-size: 0.75rem;
            background: #fee2e2;
            color: #991b1b;
            padding: 4px 12px;
            border-radius: 9999px;
            font-weight: 500;
        }
        .dark .empresa-card .badge-inactivo {
            background: #7f1d1d;
            color: #fca5a5;
        }

        .empresa-card .badge-inactivo .dot {
            width: 8px;
            height: 8px;
            background: #ef4444;
            border-radius: 9999px;
            display: inline-block;
        }
        .dark .empresa-card .badge-inactivo .dot {
            background: #f87171;
        }

        /* ========================================== */
        /* SECCIONES */
        /* ========================================== */
        .empresa-card .section {
            background: white;
            border: 1px solid #e5e7eb;
            border-radius: 16px;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
            box-shadow: 0 1px 2px rgba(0,0,0,0.05);
        }
        .dark .empresa-card .section {
            background: #1f2937;
            border-color: #374151;
        }

        .empresa-card .section-title {
            font-size: 0.75rem;
            font-weight: 600;
            color: #9ca3af;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            margin: 0 0 1rem 0;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        .dark .empresa-card .section-title {
            color: #6b7280;
        }

        .empresa-card .section-title .icon {
            font-size: 1.25rem;
        }

        .empresa-card .grid-2 {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1rem;
        }

        .empresa-card .grid-3 {
            display: grid;
            grid-template-columns: 1fr 1fr 1fr;
            gap: 0.75rem;
        }

        .empresa-card .info-item {
            display: flex;
            align-items: flex-start;
            gap: 0.75rem;
            background: #f9fafb;
            padding: 0.75rem;
            border-radius: 12px;
        }
        .dark .empresa-card .info-item {
            background: #111827;
        }

        .empresa-card .info-item .emoji {
            font-size: 1.25rem;
            color: #9ca3af;
        }
        .dark .empresa-card .info-item .emoji {
            color: #6b7280;
        }

        .empresa-card .info-item .label {
            font-size: 0.65rem;
            font-weight: 500;
            color: #9ca3af;
            text-transform: uppercase;
            margin: 0;
            letter-spacing: 0.05em;
        }
        .dark .empresa-card .info-item .label {
            color: #6b7280;
        }

        .empresa-card .info-item .value {
            font-size: 0.875rem;
            font-weight: 500;
            color: #374151;
            margin: 0;
        }
        .dark .empresa-card .info-item .value {
            color: #d1d5db;
        }

        /* ========================================== */
        /* SERVICIOS */
        /* ========================================== */
        .empresa-card .service-card {
            padding: 1rem;
            border-radius: 12px;
            border: 1px solid #e5e7eb;
            text-align: center;
            background: #f9fafb;
            opacity: 0.6;
        }
        .dark .empresa-card .service-card {
            background: #111827;
            border-color: #374151;
        }

        .empresa-card .service-card.active {
            opacity: 1;
        }

        .empresa-card .service-card.active-green {
            background: #f0fdf4;
            border-color: #bbf7d0;
        }
        .dark .empresa-card .service-card.active-green {
            background: #064e3b;
            border-color: #065f46;
        }

        .empresa-card .service-card.active-amber {
            background: #fffbeb;
            border-color: #fde68a;
        }
        .dark .empresa-card .service-card.active-amber {
            background: #78350f;
            border-color: #92400e;
        }

        .empresa-card .service-card.active-purple {
            background: #faf5ff;
            border-color: #e9d5ff;
        }
        .dark .empresa-card .service-card.active-purple {
            background: #3b0764;
            border-color: #581c87;
        }

        .empresa-card .service-card .emoji-big {
            font-size: 1.5rem;
            margin-bottom: 4px;
        }

        .empresa-card .service-card .name {
            font-size: 0.875rem;
            font-weight: 600;
            color: #9ca3af;
            margin: 0;
        }
        .dark .empresa-card .service-card .name {
            color: #6b7280;
        }

        .empresa-card .service-card .name.green {
            color: #15803d;
        }
        .dark .empresa-card .service-card .name.green {
            color: #34d399;
        }

        .empresa-card .service-card .name.amber {
            color: #b45309;
        }
        .dark .empresa-card .service-card .name.amber {
            color: #fbbf24;
        }

        .empresa-card .service-card .name.purple {
            color: #7e22ce;
        }
        .dark .empresa-card .service-card .name.purple {
            color: #a78bfa;
        }

        .empresa-card .service-card .status {
            font-size: 0.75rem;
            color: #9ca3af;
        }
        .dark .empresa-card .service-card .status {
            color: #6b7280;
        }

        .empresa-card .service-card .status.green {
            color: #16a34a;
        }
        .dark .empresa-card .service-card .status.green {
            color: #34d399;
        }

        .empresa-card .service-card .status.amber {
            color: #d97706;
        }
        .dark .empresa-card .service-card .status.amber {
            color: #fbbf24;
        }

        .empresa-card .service-card .status.purple {
            color: #9333ea;
        }
        .dark .empresa-card .service-card .status.purple {
            color: #a78bfa;
        }

        /* ========================================== */
        /* FOOTER */
        /* ========================================== */
        .empresa-card .footer {
            background: rgba(249, 250, 251, 0.5);
            border: 1px solid #e5e7eb;
            border-radius: 16px;
            padding: 1rem;
            box-shadow: 0 1px 2px rgba(0,0,0,0.05);
        }
        .dark .empresa-card .footer {
            background: rgba(17, 24, 39, 0.5);
            border-color: #374151;
        }

        .empresa-card .footer-content {
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            gap: 0.5rem;
            font-size: 0.75rem;
            color: #6b7280;
        }
        .dark .empresa-card .footer-content {
            color: #9ca3af;
        }

        .empresa-card .footer-row {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            gap: 1rem;
        }

        .empresa-card .footer-label {
            font-weight: 500;
            color: #6b7280;
        }
        .dark .empresa-card .footer-label {
            color: #9ca3af;
        }

        .empresa-card .footer-sep {
            display: none;
            color: #d1d5db;
        }
        .dark .empresa-card .footer-sep {
            color: #374151;
        }

        .empresa-card .footer-time {
            color: #9ca3af;
        }
        .dark .empresa-card .footer-time {
            color: #6b7280;
        }

        /* ========================================== */
        /* RESPONSIVE */
        /* ========================================== */
        @media (max-width: 640px) {
            .empresa-card .grid-2 {
                grid-template-columns: 1fr;
            }
            .empresa-card .grid-3 {
                grid-template-columns: 1fr;
            }
            .empresa-card .footer-sep {
                display: none;
            }
        }
        @media (min-width: 640px) {
            .empresa-card .footer-sep {
                display: inline;
            }
            .empresa-card .footer-content {
                flex-direction: row;
                align-items: center;
            }
        }
    </style>

    @php
        $empresa = $this->record;
        $fechaConvenio = $empresa->fecha_termino_convenio
            ? \Carbon\Carbon::parse($empresa->fecha_termino_convenio)
            : null;
        $estaVencido = $fechaConvenio ? $fechaConvenio->isPast() : false;
    @endphp

    <div class="empresa-card">

        <!-- HEADER -->
        <div class="header">
            <div class="header-content">
                <div class="logo">
                    <span>🏢</span>
                </div>
                <div>
                    <h1 class="nombre">{{ $empresa->nombre }}</h1>
                    <div class="badges">
                        <span class="badge-id">ID: #{{ $empresa->id }}</span>
                        @if($empresa->activo)
                            <span class="badge-activo">
                                <span class="dot"></span>
                                Activo
                            </span>
                        @else
                            <span class="badge-inactivo">
                                <span class="dot"></span>
                                Inactivo
                            </span>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- CONTACTO -->
        <div class="section">
            <h3 class="section-title">
                <span class="icon">📋</span> Información de Contacto
            </h3>
            <div class="grid-2">
                <div class="info-item">
                    <span class="emoji">📍</span>
                    <div>
                        <p class="label">Dirección</p>
                        <p class="value">{{ $empresa->direccion ?? '—' }}</p>
                    </div>
                </div>
                <div class="info-item">
                    <span class="emoji">📞</span>
                    <div>
                        <p class="label">Teléfono</p>
                        <p class="value">{{ $empresa->telefono ?? '—' }}</p>
                    </div>
                </div>
                <div class="info-item">
                    <span class="emoji">👤</span>
                    <div>
                        <p class="label">Persona de Contacto</p>
                        <p class="value">{{ $empresa->contacto ?? '—' }}</p>
                    </div>
                </div>
                <div class="info-item">
                    <span class="emoji">📅</span>
                    <div>
                        <p class="label">Vigencia del Convenio</p>
                        @if($fechaConvenio)
                            <p class="value" style="color: {{ $estaVencido ? '#dc2626' : '#16a34a' }};">
                                {{ $fechaConvenio->format('d/m/Y') }}
                                @if($estaVencido)
                                    <span style="font-size: 0.65rem; background: #fee2e2; color: #dc2626; padding: 2px 8px; border-radius: 9999px; font-weight: 700;">VENCIDO</span>
                                @else
                                    <span style="font-size: 0.65rem; background: #dcfce7; color: #16a34a; padding: 2px 8px; border-radius: 9999px; font-weight: 700;">VIGENTE</span>
                                @endif
                            </p>
                        @else
                            <p class="value" style="color: #6b7280;">♾️ Vigencia indefinida</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- SERVICIOS -->
        <div class="section">
            <h3 class="section-title">
                <span class="icon">🛠️</span> Servicios Disponibles
            </h3>
            <div class="grid-3">
                <div class="service-card {{ $empresa->servicio_social ? 'active active-green' : '' }}">
                    <div class="emoji-big">{{ $empresa->servicio_social ? '🟢' : '⚪' }}</div>
                    <p class="name {{ $empresa->servicio_social ? 'green' : '' }}">Servicio Social</p>
                    <span class="status {{ $empresa->servicio_social ? 'green' : '' }}">
                        {{ $empresa->servicio_social ? '✅ Disponible' : 'No disponible' }}
                    </span>
                </div>

                <div class="service-card {{ $empresa->practicas ? 'active active-amber' : '' }}">
                    <div class="emoji-big">{{ $empresa->practicas ? '🟠' : '⚪' }}</div>
                    <p class="name {{ $empresa->practicas ? 'amber' : '' }}">Prácticas Profesionales</p>
                    <span class="status {{ $empresa->practicas ? 'amber' : '' }}">
                        {{ $empresa->practicas ? '✅ Disponible' : 'No disponible' }}
                    </span>
                </div>

                <div class="service-card {{ $empresa->programa_dual ? 'active active-purple' : '' }}">
                    <div class="emoji-big">{{ $empresa->programa_dual ? '🟣' : '⚪' }}</div>
                    <p class="name {{ $empresa->programa_dual ? 'purple' : '' }}">Programa Dual</p>
                    <span class="status {{ $empresa->programa_dual ? 'purple' : '' }}">
                        {{ $empresa->programa_dual ? '✅ Disponible' : 'No disponible' }}
                    </span>
                </div>
            </div>
        </div>

        <!-- FOOTER -->
        <div class="footer">
            <div class="footer-content">
                <div class="footer-row">
                    <span>
                        <span class="footer-label">Ingresado al sistema:</span>
                        {{ $empresa->created_at->format('d/m/Y \a \l\a\s H:i \h\r\s') }}
                    </span>
                    <span class="footer-sep">|</span>
                    <span>
                        <span class="footer-label">Última actualización:</span>
                        {{ $empresa->updated_at->format('d/m/Y \a \l\a\s H:i \h\r\s') }}
                    </span>
                </div>
                <span class="footer-time">
                    {{ $empresa->created_at->diffForHumans() }}
                </span>
            </div>
        </div>

    </div>
</x-filament-panels::page>