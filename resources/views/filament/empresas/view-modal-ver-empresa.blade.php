{{-- resources/views/filament/empresas/view-modal-ver-empresa.blade.php --}}
<x-filament-panels::page>
    @php
        $empresa = $this->record;
        $fechaConvenio = $empresa->fecha_termino_convenio 
            ? \Carbon\Carbon::parse($empresa->fecha_termino_convenio) 
            : null;
        $estaVencido = $fechaConvenio ? $fechaConvenio->isPast() : false;
    @endphp

    <div style="max-width: 1024px; margin: 0 auto; padding: 0 1rem;">

        <!-- ========================================== -->
        <!-- TARJETA DE PRESENTACIÓN -->
        <!-- ========================================== -->
        <div style="background: linear-gradient(135deg, #ecfdf5, #d1fae5); border: 1px solid #bbf7d0; border-radius: 16px; padding: 1.5rem; margin-bottom: 1.5rem; box-shadow: 0 1px 2px rgba(0,0,0,0.05);">
            <div style="display: flex; align-items: center; gap: 1rem;">
                <div style="width: 64px; height: 64px; background: linear-gradient(135deg, #10b981, #15803d); border-radius: 12px; display: flex; align-items: center; justify-content: center; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1);">
                    <span style="font-size: 2rem;">🏢</span>
                </div>
                <div>
                    <h1 style="font-size: 1.5rem; font-weight: 700; color: #1f2937; margin: 0;">{{ $empresa->nombre }}</h1>
                    <div style="display: flex; align-items: center; gap: 0.75rem; margin-top: 4px;">
                        <span style="font-size: 0.75rem; font-family: monospace; background: #f3f4f6; padding: 2px 8px; border-radius: 4px; color: #6b7280;">ID: #{{ $empresa->id }}</span>
                        @if($empresa->activo)
                            <span style="display: inline-flex; align-items: center; gap: 4px; font-size: 0.75rem; background: #d1fae5; color: #065f46; padding: 4px 12px; border-radius: 9999px; font-weight: 500;">
                                <span style="width: 8px; height: 8px; background: #10b981; border-radius: 9999px; display: inline-block;"></span>
                                Activo
                            </span>
                        @else
                            <span style="display: inline-flex; align-items: center; gap: 4px; font-size: 0.75rem; background: #fee2e2; color: #991b1b; padding: 4px 12px; border-radius: 9999px; font-weight: 500;">
                                <span style="width: 8px; height: 8px; background: #ef4444; border-radius: 9999px; display: inline-block;"></span>
                                Inactivo
                            </span>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- ========================================== -->
        <!-- DATOS DE CONTACTO -->
        <!-- ========================================== -->
        <div style="background: white; border: 1px solid #e5e7eb; border-radius: 16px; padding: 1.5rem; margin-bottom: 1.5rem; box-shadow: 0 1px 2px rgba(0,0,0,0.05);">
            <h3 style="font-size: 0.75rem; font-weight: 600; color: #9ca3af; text-transform: uppercase; letter-spacing: 0.05em; margin: 0 0 1rem 0;">📋 Información de Contacto</h3>
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                <!-- Dirección -->
                <div style="display: flex; align-items: flex-start; gap: 0.75rem; background: #f9fafb; padding: 0.75rem; border-radius: 12px;">
                    <span style="font-size: 1.25rem; color: #9ca3af;">📍</span>
                    <div>
                        <p style="font-size: 0.65rem; font-weight: 500; color: #9ca3af; text-transform: uppercase; margin: 0; letter-spacing: 0.05em;">Dirección</p>
                        <p style="font-size: 0.875rem; font-weight: 500; color: #374151; margin: 0;">{{ $empresa->direccion ?? '—' }}</p>
                    </div>
                </div>

                <!-- Teléfono -->
                <div style="display: flex; align-items: flex-start; gap: 0.75rem; background: #f9fafb; padding: 0.75rem; border-radius: 12px;">
                    <span style="font-size: 1.25rem; color: #9ca3af;">📞</span>
                    <div>
                        <p style="font-size: 0.65rem; font-weight: 500; color: #9ca3af; text-transform: uppercase; margin: 0; letter-spacing: 0.05em;">Teléfono</p>
                        <p style="font-size: 0.875rem; font-weight: 500; color: #374151; margin: 0;">{{ $empresa->telefono ?? '—' }}</p>
                    </div>
                </div>

                <!-- Contacto -->
                <div style="display: flex; align-items: flex-start; gap: 0.75rem; background: #f9fafb; padding: 0.75rem; border-radius: 12px;">
                    <span style="font-size: 1.25rem; color: #9ca3af;">👤</span>
                    <div>
                        <p style="font-size: 0.65rem; font-weight: 500; color: #9ca3af; text-transform: uppercase; margin: 0; letter-spacing: 0.05em;">Persona de Contacto</p>
                        <p style="font-size: 0.875rem; font-weight: 500; color: #374151; margin: 0;">{{ $empresa->contacto ?? '—' }}</p>
                    </div>
                </div>

                <!-- Vigencia -->
                <div style="display: flex; align-items: flex-start; gap: 0.75rem; background: #f9fafb; padding: 0.75rem; border-radius: 12px;">
                    <span style="font-size: 1.25rem; color: #9ca3af;">📅</span>
                    <div>
                        <p style="font-size: 0.65rem; font-weight: 500; color: #9ca3af; text-transform: uppercase; margin: 0; letter-spacing: 0.05em;">Vigencia del Convenio</p>
                        @if($fechaConvenio)
                            <p style="font-size: 0.875rem; font-weight: 500; margin: 0; color: {{ $estaVencido ? '#dc2626' : '#16a34a' }};">
                                {{ $fechaConvenio->format('d/m/Y') }}
                                @if($estaVencido)
                                    <span style="font-size: 0.65rem; background: #fee2e2; color: #dc2626; padding: 2px 8px; border-radius: 9999px; font-weight: 700;">VENCIDO</span>
                                @else
                                    <span style="font-size: 0.65rem; background: #dcfce7; color: #16a34a; padding: 2px 8px; border-radius: 9999px; font-weight: 700;">VIGENTE</span>
                                @endif
                            </p>
                        @else
                            <p style="font-size: 0.875rem; font-weight: 500; color: #6b7280; margin: 0;">♾️ Vigencia indefinida</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- ========================================== -->
        <!-- SERVICIOS DISPONIBLES -->
        <!-- ========================================== -->
        <div style="background: white; border: 1px solid #e5e7eb; border-radius: 16px; padding: 1.5rem; margin-bottom: 1.5rem; box-shadow: 0 1px 2px rgba(0,0,0,0.05);">
            <h3 style="font-size: 0.75rem; font-weight: 600; color: #9ca3af; text-transform: uppercase; letter-spacing: 0.05em; margin: 0 0 1rem 0;">🛠️ Servicios Disponibles</h3>
            <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 0.75rem;">
                <!-- Servicio Social -->
                <div style="padding: 1rem; border-radius: 12px; border: 1px solid {{ $empresa->servicio_social ? '#bbf7d0' : '#e5e7eb' }}; text-align: center; background: {{ $empresa->servicio_social ? '#f0fdf4' : '#f9fafb' }}; opacity: {{ $empresa->servicio_social ? '1' : '0.6' }};">
                    <div style="font-size: 1.5rem; margin-bottom: 4px;">{{ $empresa->servicio_social ? '🟢' : '⚪' }}</div>
                    <p style="font-size: 0.875rem; font-weight: 600; color: {{ $empresa->servicio_social ? '#15803d' : '#9ca3af' }}; margin: 0;">Servicio Social</p>
                    <span style="font-size: 0.75rem; color: {{ $empresa->servicio_social ? '#16a34a' : '#9ca3af' }};">
                        {{ $empresa->servicio_social ? '✅ Disponible' : 'No disponible' }}
                    </span>
                </div>

                <!-- Prácticas -->
                <div style="padding: 1rem; border-radius: 12px; border: 1px solid {{ $empresa->practicas ? '#fde68a' : '#e5e7eb' }}; text-align: center; background: {{ $empresa->practicas ? '#fffbeb' : '#f9fafb' }}; opacity: {{ $empresa->practicas ? '1' : '0.6' }};">
                    <div style="font-size: 1.5rem; margin-bottom: 4px;">{{ $empresa->practicas ? '🟠' : '⚪' }}</div>
                    <p style="font-size: 0.875rem; font-weight: 600; color: {{ $empresa->practicas ? '#b45309' : '#9ca3af' }}; margin: 0;">Prácticas Profesionales</p>
                    <span style="font-size: 0.75rem; color: {{ $empresa->practicas ? '#d97706' : '#9ca3af' }};">
                        {{ $empresa->practicas ? '✅ Disponible' : 'No disponible' }}
                    </span>
                </div>

                <!-- Dual -->
                <div style="padding: 1rem; border-radius: 12px; border: 1px solid {{ $empresa->programa_dual ? '#e9d5ff' : '#e5e7eb' }}; text-align: center; background: {{ $empresa->programa_dual ? '#faf5ff' : '#f9fafb' }}; opacity: {{ $empresa->programa_dual ? '1' : '0.6' }};">
                    <div style="font-size: 1.5rem; margin-bottom: 4px;">{{ $empresa->programa_dual ? '🟣' : '⚪' }}</div>
                    <p style="font-size: 0.875rem; font-weight: 600; color: {{ $empresa->programa_dual ? '#7e22ce' : '#9ca3af' }}; margin: 0;">Programa Dual</p>
                    <span style="font-size: 0.75rem; color: {{ $empresa->programa_dual ? '#9333ea' : '#9ca3af' }};">
                        {{ $empresa->programa_dual ? '✅ Disponible' : 'No disponible' }}
                    </span>
                </div>
            </div>
        </div>

        <!-- ========================================== -->
        <!-- INFORMACIÓN DEL SISTEMA -->
        <!-- ========================================== -->
        <div style="background: rgba(249, 250, 251, 0.5); border: 1px solid #e5e7eb; border-radius: 16px; padding: 1rem; box-shadow: 0 1px 2px rgba(0,0,0,0.05);">
            <div style="display: flex; flex-direction: column; justify-content: space-between; gap: 0.5rem; font-size: 0.75rem; color: #6b7280;">
                <div style="display: flex; flex-wrap: wrap; align-items: center; gap: 1rem;">
                    <span>
                        <span style="font-weight: 500; color: #6b7280;">Ingresado al sistema:</span>
                        {{ $empresa->created_at->format('d/m/Y \a \l\a\s H:i \h\r\s') }}
                    </span>
                    <span style="display: none; color: #d1d5db;">|</span>
                    <span>
                        <span style="font-weight: 500; color: #6b7280;">Última actualización:</span>
                        {{ $empresa->updated_at->format('d/m/Y \a \l\a\s H:i \h\r\s') }}
                    </span>
                </div>
                <span style="color: #9ca3af;">
                    {{ $empresa->created_at->diffForHumans() }}
                </span>
            </div>
        </div>
    </div>
</x-filament-panels::page>