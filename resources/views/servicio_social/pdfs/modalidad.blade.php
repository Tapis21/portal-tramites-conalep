<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Elección de Modalidad - {{ $tipo_tramite }}</title>
    <style>
        @page {
            size: letter;
            margin: 40pt 50pt 40pt 50pt;
        }
        body {
            font-family: 'Arial', sans-serif;
            font-size: 11pt;
            color: #1f2937;
            line-height: 1.5;
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
        .field {
            margin-bottom: 10px;
        }
        .field-label {
            font-weight: bold;
            display: inline-block;
            width: 180px;
        }
        .field-value {
            display: inline-block;
            color: #0066cc;
            font-weight: 500;
        }
        .signature {
            margin-top: 40px;
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
        <h1>Elección de Modalidad</h1>
        <p>{{ $tipo_tramite }} - Plantel Cancún II</p>
    </div>

    <div class="content">
        <p>El estudiante <strong class="field-value">{{ $nombre_completo }}</strong>, con matrícula <strong class="field-value">{{ $matricula }}</strong>, de la carrera de <strong class="field-value">{{ $carrera }}</strong>, del grupo <strong class="field-value">{{ $grupo }}</strong>, turno <strong class="field-value">{{ $turno }}</strong>, semestre <strong class="field-value">{{ $semestre }}°</strong>, generación <strong class="field-value">{{ $generacion }}</strong>, elige la siguiente modalidad para realizar su {{ $tipo_tramite }}:</p>

        <div class="field">
            <span class="field-label">Modalidad elegida:</span>
            <span class="field-value">_________________________________</span>
        </div>

        <div class="field">
            <span class="field-label">Institución/Empresa:</span>
            <span class="field-value">{{ $empresa }}</span>
        </div>

        <div class="field">
            <span class="field-label">Área asignada:</span>
            <span class="field-value">{{ $area_asignada }}</span>
        </div>

        <div class="field">
            <span class="field-label">Jefe inmediato:</span>
            <span class="field-value">{{ $grado_academico_jefe }} {{ $nombre_jefe_inmediato }}</span>
        </div>

        <div class="field">
            <span class="field-label">Horario:</span>
            <span class="field-value">{{ $horario }}</span>
        </div>

        <p style="margin-top:30px;">Fecha de inicio: <strong class="field-value">{{ $fecha_inicio }}</strong></p>
        <p>Fecha de finalización: <strong class="field-value">{{ $fecha_finalizacion }}</strong></p>

        <div class="signature">
            <div class="signature-box">
                <div class="signature-line"></div>
                <span style="font-size:10pt;">Firma del estudiante</span>
            </div>
            <div class="signature-box">
                <div class="signature-line"></div>
                <span style="font-size:10pt;">Vo.Bo. Jefe de Proyecto</span>
            </div>
        </div>
    </div>

    <div class="footer">
        <p>CONALEP Plantel Cancún II - Región 228, Mza 5, Lote 1, Av. 20 de Nov. Entre costa maya y calle 61, zona 4, Cancún, Q. Roo, CP 77516</p>
        <p>Tel. (01 998) 2710194 | vinculacion.cancun2@qroo.conalep.edu.mx</p>
    </div>
</body>
</html>