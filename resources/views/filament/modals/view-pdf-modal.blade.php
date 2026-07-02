{{-- resources/views/filament/modals/view-pdf-modal.blade.php --}}
@php
    $servicioSocial = \App\Models\ServicioSocial::where('user_id', $record->user_id)->first();
    $pdfUrl = asset('storage/' . $record->archivo_pdf);
    
    $empresaNombre = $servicioSocial && $servicioSocial->empresa
        ? $servicioSocial->empresa->nombre
        : 'No asignada';
    $areaAsignada = $servicioSocial?->area_asignada ?? 'No asignada';
    $fechaInicio = $servicioSocial?->fecha_inicio
        ? date('d/m/Y', strtotime($servicioSocial->fecha_inicio))
        : 'No definida';
    $fechaFinalizacion = $servicioSocial?->fecha_limite_segundo_informe
        ? date('d/m/Y', strtotime($servicioSocial->fecha_limite_segundo_informe))
        : 'No definida';
    $horario = $servicioSocial?->horario
        ? $servicioSocial->horario->hora_inicio . ' - ' . $servicioSocial->horario->hora_fin
        : 'No definido';
    $contacto = $servicioSocial?->nombre_persona_carta ?? 'No definido';

    // Colores de estado
    $statusColors = [
        'validado' => ['bg' => '#dcfce7', 'text' => '#166534'],
        'validado_ventanilla' => ['bg' => '#dbeafe', 'text' => '#1e40af'],
        'rechazado' => ['bg' => '#fee2e2', 'text' => '#991b1b'],
        'pendiente' => ['bg' => '#fef9c3', 'text' => '#854d0e'],
    ];
    $statusLabels = [
        'validado' => 'Validado',
        'validado_ventanilla' => 'Validado en Ventanilla',
        'rechazado' => 'Rechazado',
        'pendiente' => 'Pendiente',
    ];
    $color = $statusColors[$record->estatus] ?? ['bg' => '#f3f4f6', 'text' => '#374151'];
    $label = $statusLabels[$record->estatus] ?? $record->estatus;
@endphp

<div style="padding: 8px;">

    <!-- Encabezado -->
    <div style="display: flex; align-items: center; gap: 16px; border-bottom: 2px solid #e5e7eb; padding-bottom: 16px; margin-bottom: 16px;">
        <div style="display: flex; align-items: center; gap: 12px;">
            <div style="padding: 12px; background: #e8f5e9; border-radius: 12px; display: flex; align-items: center; justify-content: center;">
                <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="#059669" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                    <polyline points="14 2 14 8 20 8"/>
                    <line x1="16" y1="13" x2="8" y2="13"/>
                    <line x1="16" y1="17" x2="8" y2="17"/>
                    <polyline points="10 9 9 9 8 9"/>
                </svg>
            </div>
            <div>
                <h3 style="font-size: 24px; font-weight: bold; color: #111827; margin: 0;">Vista del Documento</h3>
                <p style="font-size: 14px; color: #6b7280; font-weight: 500; margin: 0;">{{ $record->tipoDocumento->nombre }}</p>
            </div>
        </div>
    </div>

    <!-- Grid de Información del Documento -->
    <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 16px; margin-bottom: 16px;">
        <div style="background: white; border-radius: 12px; border: 1px solid #e5e7eb; padding: 16px; box-shadow: 0 1px 2px rgba(0,0,0,0.05);">
            <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 8px;">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#6b7280" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                    <polyline points="14 2 14 8 20 8"/>
                    <line x1="16" y1="13" x2="8" y2="13"/>
                    <line x1="16" y1="17" x2="8" y2="17"/>
                    <polyline points="10 9 9 9 8 9"/>
                </svg>
                <span style="font-size: 11px; font-weight: 600; color: #6b7280; text-transform: uppercase; letter-spacing: 0.05em;">Documento</span>
            </div>
            <p style="font-size: 14px; font-weight: 500; color: #111827; margin: 0;">{{ $record->tipoDocumento->nombre }}</p>
        </div>
        <div style="background: white; border-radius: 12px; border: 1px solid #e5e7eb; padding: 16px; box-shadow: 0 1px 2px rgba(0,0,0,0.05);">
            <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 8px;">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#6b7280" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
                    <polyline points="9 12 11 14 15 10"/>
                </svg>
                <span style="font-size: 11px; font-weight: 600; color: #6b7280; text-transform: uppercase; letter-spacing: 0.05em;">Estado</span>
            </div>
            <span style="display: inline-block; padding: 4px 12px; border-radius: 9999px; font-size: 12px; font-weight: 500; background: {{ $color['bg'] }}; color: {{ $color['text'] }};">
                {{ $label }}
            </span>
        </div>
        <div style="background: white; border-radius: 12px; border: 1px solid #e5e7eb; padding: 16px; box-shadow: 0 1px 2px rgba(0,0,0,0.05);">
            <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 8px;">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#6b7280" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <rect x="3" y="4" width="18" height="18" rx="2" ry="2"/>
                    <line x1="16" y1="2" x2="16" y2="6"/>
                    <line x1="8" y1="2" x2="8" y2="6"/>
                    <line x1="3" y1="10" x2="21" y2="10"/>
                </svg>
                <span style="font-size: 11px; font-weight: 600; color: #6b7280; text-transform: uppercase; letter-spacing: 0.05em;">Fecha de subida</span>
            </div>
            <p style="font-size: 14px; font-weight: 500; color: #111827; margin: 0;">{{ $record->created_at->format('d/m/Y H:i') }}</p>
        </div>
        <div style="background: white; border-radius: 12px; border: 1px solid #e5e7eb; padding: 16px; box-shadow: 0 1px 2px rgba(0,0,0,0.05);">
            <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 8px;">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#6b7280" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
                    <circle cx="12" cy="7" r="4"/>
                </svg>
                <span style="font-size: 11px; font-weight: 600; color: #6b7280; text-transform: uppercase; letter-spacing: 0.05em;">Estudiante</span>
            </div>
            <p style="font-size: 14px; font-weight: 500; color: #111827; margin: 0;">{{ $record->user->name }}</p>
        </div>
    </div>

    <!-- Información del Servicio Social -->
    <div style="background: #eff6ff; border-radius: 12px; border: 1px solid #bfdbfe; overflow: hidden; margin-bottom: 16px; box-shadow: 0 1px 2px rgba(0,0,0,0.05);">
        <div style="padding: 12px 20px; background: #dbeafe; border-bottom: 1px solid #bfdbfe;">
            <div style="display: flex; align-items: center; gap: 12px;">
                <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#1e40af" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <rect x="2" y="7" width="20" height="14" rx="2" ry="2"/>
                    <path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/>
                </svg>
                <span style="font-size: 14px; font-weight: 700; color: #1e40af; text-transform: uppercase; letter-spacing: 0.05em;">Datos del Servicio Social</span>
            </div>
        </div>
        <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px; padding: 20px;">
            <div>
                <span style="font-size: 11px; font-weight: 600; color: #2563eb; text-transform: uppercase; letter-spacing: 0.05em;">Empresa</span>
                <p style="font-size: 14px; font-weight: 500; color: #111827; margin: 4px 0 0 0;">{{ $empresaNombre }}</p>
            </div>
            <div>
                <span style="font-size: 11px; font-weight: 600; color: #2563eb; text-transform: uppercase; letter-spacing: 0.05em;">Área asignada</span>
                <p style="font-size: 14px; font-weight: 500; color: #111827; margin: 4px 0 0 0;">{{ $areaAsignada }}</p>
            </div>
            <div>
                <span style="font-size: 11px; font-weight: 600; color: #2563eb; text-transform: uppercase; letter-spacing: 0.05em;">Fecha de inicio</span>
                <p style="font-size: 14px; font-weight: 500; color: #111827; margin: 4px 0 0 0;">{{ $fechaInicio }}</p>
            </div>
            <div>
                <span style="font-size: 11px; font-weight: 600; color: #2563eb; text-transform: uppercase; letter-spacing: 0.05em;">Fecha de finalización</span>
                <p style="font-size: 14px; font-weight: 500; color: #111827; margin: 4px 0 0 0;">{{ $fechaFinalizacion }}</p>
            </div>
            <div>
                <span style="font-size: 11px; font-weight: 600; color: #2563eb; text-transform: uppercase; letter-spacing: 0.05em;">Horario</span>
                <p style="font-size: 14px; font-weight: 500; color: #111827; margin: 4px 0 0 0;">{{ $horario }}</p>
            </div>
            <div>
                <span style="font-size: 11px; font-weight: 600; color: #2563eb; text-transform: uppercase; letter-spacing: 0.05em;">Contacto</span>
                <p style="font-size: 14px; font-weight: 500; color: #111827; margin: 4px 0 0 0;">{{ $contacto }}</p>
            </div>
        </div>
    </div>

    <!-- Visualizador del PDF -->
    <div style="background: white; border-radius: 12px; border: 1px solid #e5e7eb; overflow: hidden; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1), 0 2px 4px -1px rgba(0,0,0,0.06);">
        <div style="height: 600px; background: #f3f4f6;">
            <iframe 
                src="{{ $pdfUrl }}#toolbar=1&navpanes=1&scrollbar=1" 
                type="application/pdf" 
                width="100%" 
                height="100%"
                style="border: none;"
            ></iframe>
        </div>
        <div style="display: flex; align-items: center; justify-content: space-between; padding: 12px 20px; background: #f9fafb; border-top: 1px solid #e5e7eb;">
            <div style="display: flex; align-items: center; gap: 8px;">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#6b7280" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                    <polyline points="14 2 14 8 20 8"/>
                    <line x1="16" y1="13" x2="8" y2="13"/>
                    <line x1="16" y1="17" x2="8" y2="17"/>
                    <polyline points="10 9 9 9 8 9"/>
                </svg>
                <span style="font-size: 12px; color: #6b7280; font-weight: 500;">Documento en formato PDF</span>
            </div>
            <div style="display: flex; align-items: center; gap: 8px;">
                <a href="{{ $pdfUrl }}" target="_blank" style="display: inline-flex; align-items: center; gap: 8px; padding: 8px 16px; font-size: 14px; font-weight: 500; color: white; background: #059669; border-radius: 8px; text-decoration: none; transition: all 0.2s;">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                        <circle cx="12" cy="12" r="3"/>
                    </svg>
                    Ver PDF
                </a>
                <a href="{{ $pdfUrl }}" download style="display: inline-flex; align-items: center; gap: 8px; padding: 8px 16px; font-size: 14px; font-weight: 500; color: #374151; background: #f3f4f6; border: 1px solid #e5e7eb; border-radius: 8px; text-decoration: none; transition: all 0.2s;">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
                        <polyline points="7 10 12 15 17 10"/>
                        <line x1="12" y1="15" x2="12" y2="3"/>
                    </svg>
                    Descargar
                </a>
            </div>
        </div>
    </div>

</div>