<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Evaluación de Competencias - {{ $tipo_tramite }}</title>
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
        .field-value {
            color: #0066cc;
            font-weight: 500;
        }
        .footer {
            margin-top: 40px;
            text-align: center;
            font-size: 9pt;
            color: #9ca3af;
            border-top: 1px solid #e5e7eb;
            padding-top: 15px;
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
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }
        th {
            background-color: #006837;
            color: white;
            padding: 8px;
            text-align: left;
            font-size: 10pt;
        }
        td {
            border: 1px solid #d1d5db;
            padding: 6px 8px;
            font-size: 9.5pt;
        }
        .evaluation-scale {
            text-align: center;
            font-size: 8pt;
            color: #6b7280;
        }
    </style>
</head>
<body>
    <div class="header">
        <img src="{{ public_path('images/Quintana-Roo_3.png') }}" alt="CONALEP">
        <h1>Evaluación de Competencias del Desempeño</h1>
        <p>{{ $tipo_tramite }} - Plantel Cancún II</p>
    </div>

    <div class="content">
        <p><strong>Estudiante:</strong> <span class="field-value">{{ $nombre_completo }}</span></p>
        <p><strong>Matrícula:</strong> <span class="field-value">{{ $matricula }}</span></p>
        <p><strong>Carrera:</strong> <span class="field-value">{{ $carrera }}</span></p>
        <p><strong>Grupo:</strong> <span class="field-value">{{ $grupo }}</span></p>
        <p><strong>Semestre:</strong> <span class="field-value">{{ $semestre }}°</span></p>
        <p><strong>Generación:</strong> <span class="field-value">{{ $generacion }}</span></p>
        <p><strong>Empresa/Institución:</strong> <span class="field-value">{{ $empresa }}</span></p>
        <p><strong>Área asignada:</strong> <span class="field-value">{{ $area_asignada }}</span></p>
        <p><strong>Jefe inmediato:</strong> <span class="field-value">{{ $grado_academico_jefe }} {{ $nombre_jefe_inmediato }}</span></p>
        <p><strong>Período evaluado:</strong> <span class="field-value">{{ $fecha_inicio }}</span> al <span class="field-value">{{ $fecha_finalizacion }}</span></p>

        <h2 style="color:#006837; margin-top:20px;">Competencias evaluadas</h2>

        <table>
            <thead>
                <tr>
                    <th style="width:60%;">Competencia</th>
                    <th style="width:10%;text-align:center;">Excelente</th>
                    <th style="width:10%;text-align:center;">Bueno</th>
                    <th style="width:10%;text-align:center;">Regular</th>
                    <th style="width:10%;text-align:center;">Mejorable</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>Responsabilidad y puntualidad</td>
                    <td style="text-align:center;">( )</td>
                    <td style="text-align:center;">( )</td>
                    <td style="text-align:center;">( )</td>
                    <td style="text-align:center;">( )</td>
                </tr>
                <tr>
                    <td>Capacidad de aprendizaje</td>
                    <td style="text-align:center;">( )</td>
                    <td style="text-align:center;">( )</td>
                    <td style="text-align:center;">( )</td>
                    <td style="text-align:center;">( )</td>
                </tr>
                <tr>
                    <td>Iniciativa y proactividad</td>
                    <td style="text-align:center;">( )</td>
                    <td style="text-align:center;">( )</td>
                    <td style="text-align:center;">( )</td>
                    <td style="text-align:center;">( )</td>
                </tr>
                <tr>
                    <td>Trabajo en equipo</td>
                    <td style="text-align:center;">( )</td>
                    <td style="text-align:center;">( )</td>
                    <td style="text-align:center;">( )</td>
                    <td style="text-align:center;">( )</td>
                </tr>
                <tr>
                    <td>Comunicación efectiva</td>
                    <td style="text-align:center;">( )</td>
                    <td style="text-align:center;">( )</td>
                    <td style="text-align:center;">( )</td>
                    <td style="text-align:center;">( )</td>
                </tr>
                <tr>
                    <td>Cumplimiento de objetivos</td>
                    <td style="text-align:center;">( )</td>
                    <td style="text-align:center;">( )</td>
                    <td style="text-align:center;">( )</td>
                    <td style="text-align:center;">( )</td>
                </tr>
            </tbody>
        </table>

        <div class="evaluation-scale" style="margin-top:8px;">
            <span>Excelente (4) · Bueno (3) · Regular (2) · Mejorable (1)</span>
        </div>

        <h2 style="color:#006837; margin-top:20px;">Observaciones del jefe inmediato</h2>
        <p>_____________________________________________________________</p>
        <p>_____________________________________________________________</p>
        <p>_____________________________________________________________</p>

        <div class="signature">
            <div class="signature-box">
                <div class="signature-line"></div>
                <span style="font-size:10pt;">Firma del estudiante</span>
            </div>
            <div class="signature-box">
                <div class="signature-line"></div>
                <span style="font-size:10pt;">Vo.Bo. Jefe Inmediato</span>
            </div>
        </div>
    </div>

    <div class="footer">
        <p>CONALEP Plantel Cancún II - Región 228, Mza 5, Lote 1, Av. 20 de Nov. Entre costa maya y calle 61, zona 4, Cancún, Q. Roo, CP 77516</p>
        <p>Tel. (01 998) 2710194 | vinculacion.cancun2@qroo.conalep.edu.mx</p>
    </div>
</body>
</html>