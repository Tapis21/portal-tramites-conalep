<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Carta de Presentación - {{ $tipo_tramite }}</title>
    <style>
        @page {
            size: letter;
            margin: 40pt 50pt 40pt 50pt;
        }
        body {
            font-family: 'Arial', sans-serif;
            font-size: 11pt;
            color: #1f2937;
            line-height: 1.6;
        }
        .header {
            text-align: center;
            border-bottom: 2px solid #006837;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }
        .header img {
            height: 60px;
            width: auto;
        }
        .header h1 {
            font-size: 16pt;
            color: #006837;
            margin: 5px 0 0;
        }
        .header p {
            font-size: 10pt;
            color: #6b7280;
            margin: 0;
        }
        .content {
            margin-top: 20px;
        }
        .field-value {
            color: #0066cc;
            font-weight: 500;
        }
        .signature {
            margin-top: 50px;
            display: flex;
            justify-content: space-between;
        }
        .signature-box {
            text-align: center;
            width: 45%;
        }
        .signature-line {
            border-top: 1px solid #000;
            margin: 30px 0 5px;
            width: 100%;
        }
        .footer {
            margin-top: 40px;
            text-align: center;
            font-size: 9pt;
            color: #9ca3af;
            border-top: 1px solid #e5e7eb;
            padding-top: 15px;
        }
    </style>
</head>
<body>
    <div class="header">
        <img src="{{ public_path('images/Quintana-Roo_3.png') }}" alt="CONALEP">
        <h1>Carta de Presentación</h1>
        <p>{{ $tipo_tramite }} - Plantel Cancún II</p>
    </div>

    <div class="content">
        <p style="text-align:right;">Cancún, Q. Roo, {{ $fecha_inicio }}</p>

        <p><strong>{{ $grado_academico }} {{ $nombre_persona_carta }}</strong></p>
        <p><strong>{{ $cargo_persona_carta }}</strong></p>
        <p><strong>{{ $empresa }}</strong></p>
        <p>Presente</p>

        <p>Por medio de la presente, me permito presentar al <strong>estudiante</strong> <span class="field-value">{{ $nombre_completo }}</span>, con matrícula <span class="field-value">{{ $matricula }}</span>, de la carrera de <span class="field-value">{{ $carrera }}</span>, del grupo <span class="field-value">{{ $grupo }}</span>, semestre <span class="field-value">{{ $semestre }}°</span>, generación <span class="field-value">{{ $generacion }}</span>, para realizar su {{ $tipo_tramite }} en esa prestigiada institución.</p>

        <p>El periodo de {{ $tipo_tramite }} comprende desde <span class="field-value">{{ $fecha_inicio }}</span> hasta <span class="field-value">{{ $fecha_finalizacion }}</span>, en el horario de <span class="field-value">{{ $horario }}</span>, cubriendo 4 horas diarias de lunes a viernes en el área de <span class="field-value">{{ $area_asignada }}</span>.</p>

        <p>El jefe inmediato que supervisará al estudiante será <span class="field-value">{{ $grado_academico_jefe }} {{ $nombre_jefe_inmediato }}</span>, con cargo <span class="field-value">{{ $cargo_jefe_inmediato }}</span>.</p>

        <p>Agradecemos de antemano las facilidades que se brinden al estudiante para el desarrollo de sus actividades.</p>

        <p>Sin otro particular, quedo a sus órdenes.</p>

        <p>Atentamente</p>

        <div class="signature">
            <div class="signature-box">
                <div class="signature-line"></div>
                <span style="font-size:10pt;">DR. NICOLAS CANO RAMÍREZ</span><br>
                <span style="font-size:9pt; color:#6b7280;">Jefe de Proyecto de Promoción y Vinculación</span>
            </div>
            <div class="signature-box">
                <div class="signature-line"></div>
                <span style="font-size:10pt;">SELLO</span>
            </div>
        </div>
    </div>

    <div class="footer">
        <p>CONALEP Plantel Cancún II - Región 228, Mza 5, Lote 1, Av. 20 de Nov. Entre costa maya y calle 61, zona 4, Cancún, Q. Roo, CP 77516</p>
        <p>Tel. (01 998) 2710194 | vinculacion.cancun2@qroo.conalep.edu.mx</p>
    </div>
</body>
</html>