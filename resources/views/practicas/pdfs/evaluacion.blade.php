<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Evaluación - {{ $tipo_tramite }}</title>
    <style>
        /* ========================================== */
        /* CONFIGURACIÓN DE PÁGINA */
        /* ========================================== */
        @page {
            size: letter;
            margin: 8mm 12mm 8mm 12mm;
        }

        body {
            font-family: 'Arial MT', 'Arial', sans-serif;
            font-size: 9pt;
            line-height: 1.4;
            color: #000000;
            background: #ffffff;
        }

        /* ========================================== */
        /* ENCABEZADO - SIN LÍNEA NEGRA */
        /* ========================================== */
        .header {
            width: 100%;
            padding-bottom: 4px;
            margin-bottom: 4px;
            display: table;
        }

        .header-logo-cell {
            display: table-cell;
            width: 60px;
            vertical-align: middle;
            padding-right: 6px;
        }

        .header-logo-cell img {
            width: 55px;
            height: auto;
            display: block;
        }

        .header-text-cell {
            display: table-cell;
            vertical-align: middle;
        }

        .header-title {
            font-family: 'Arial Black', 'Arial', sans-serif;
            font-size: 5.5pt;
            font-weight: 900;
            color: #000000;
            letter-spacing: 0.3px;
            margin: 0;
            text-transform: uppercase;
            line-height: 1.2;
        }

        .header-subtitle {
            font-family: 'Arial MT', 'Arial', sans-serif;
            font-size: 5.5pt;
            color: #000000;
            font-weight: 600;
            letter-spacing: 0.5px;
            text-transform: uppercase;
            margin: 1px 0 0;
        }

        .header-plantel {
            font-family: 'Arial MT', 'Arial', sans-serif;
            font-size: 5.5pt;
            color: #000000;
            font-weight: 600;
            margin-top: 1px;
        }

        /* ========================================== */
        /* TÍTULO - EVALUACIÓN DE COMPETENCIAS */
        /* ========================================== */
        .titulo-evaluacion {
            font-family: 'Arial', sans-serif;
            font-size: 12pt;
            font-weight: 700;
            color: #000000;
            text-align: left;
            margin-bottom: 4px;
        }

        .parrafo-intro {
            font-family: 'Arial', sans-serif;
            font-size: 10pt;
            color: #000000;
            text-align: justify;
            margin-bottom: 6px;
            line-height: 1.4;
        }

        /* ========================================== */
        /* CHECKBOXES - SS Y PP - CENTRADOS */
        /* ========================================== */
        .checkboxes-row {
            font-family: 'Arial', sans-serif;
            font-size: 10pt;
            font-weight: 700;
            color: #000000;
            margin-bottom: 8px;
            text-align: center;
        }

        .checkbox-inline {
            display: inline-block;
            border: 1.5px solid #000000;
            width: 13px;
            height: 13px;
            text-align: center;
            line-height: 13px;
            font-size: 9pt;
            font-weight: 700;
            margin: 0 3px;
            background: #ffffff;
            font-family: 'Arial', sans-serif;
        }

        .checkbox-label {
            margin: 0 2px;
        }

        .checkbox-separator {
            display: inline-block;
            width: 25px;
        }

        /* ========================================== */
        /* TABLA DE DATOS DEL ALUMNO */
        /* ========================================== */
        .tabla-datos {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 8px;
            font-family: 'Arial', sans-serif;
            font-size: 10pt;
        }

        .tabla-datos td {
            border: 1px solid #000000;
            padding: 3px 6px;
            vertical-align: middle;
        }

        .tabla-datos .label {
            font-weight: 700;
            color: #000000;
            width: 18%;
        }

        .tabla-datos .value {
            font-weight: 400;
            color: #003399;
        }

        /* ========================================== */
        /* INSTRUCCIÓN */
        /* ========================================== */
        .instruccion {
            font-family: 'Arial', sans-serif;
            font-size: 10pt;
            font-weight: 700;
            color: #000000;
            text-align: justify;
            margin-bottom: 5px;
        }

        /* ========================================== */
        /* TABLA DE COMPETENCIAS */
        /* ========================================== */
        .tabla-competencias {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
            font-family: 'Arial', sans-serif;
        }

        .tabla-competencias th {
            border: 1px solid #000000;
            padding: 2px 4px;
            text-align: center;
            font-weight: 700;
            font-size: 8.5pt;
            background-color: #f0f0f0;
        }

        .tabla-competencias td {
            border: 1px solid #000000;
            padding: 2px 4px;
            text-align: center;
            font-size: 8.5pt;
            vertical-align: middle;
        }

        .tabla-competencias .celda-texto {
            text-align: left;
            font-size: 8.5pt;
            font-weight: 400;
            padding-left: 6px;
        }

        .tabla-competencias .celda-check {
            text-align: center;
            font-size: 8.5pt;
        }

        .tabla-competencias .fila-conocimientos td {
            border: 1px solid #000000;
            padding: 4px 6px;
            vertical-align: middle;
        }

        .tabla-competencias .fila-conocimientos .celda-texto {
            text-align: left;
            font-size: 8.5pt;
            font-weight: 400;
            padding-left: 6px;
            width: 38%;
        }

        .tabla-competencias .fila-conocimientos .celda-linea {
            text-align: left;
            font-size: 8.5pt;
            color: #555555;
            font-style: italic;
            padding: 4px 6px;
            border: 1px solid #000000;
        }

        .tabla-competencias .fila-conocimientos .celda-linea .linea {
            display: block;
            border-bottom: 1px solid #000000;
            width: 100%;
            height: 18px;
            margin-top: 2px;
        }

        /* ========================================== */
        /* TÍTULO - EVALUACIÓN DEL SERVICIO RECIBIDO */
        /* ========================================== */
        .titulo-servicio {
            font-family: 'Arial', sans-serif;
            font-size: 10pt;
            font-weight: 700;
            color: #000000;
            text-align: center;
            margin: 10px 0 5px;
        }

        /* ========================================== */
        /* TABLA DE EVALUACIÓN DEL SERVICIO */
        /* ========================================== */
        .tabla-servicio {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 12px;
            font-family: 'Arial', sans-serif;
        }

        .tabla-servicio th {
            border: 1px solid #000000;
            padding: 2px 4px;
            text-align: center;
            font-weight: 700;
            font-size: 8.5pt;
            background-color: #f0f0f0;
        }

        .tabla-servicio td {
            border: 1px solid #000000;
            padding: 2px 4px;
            text-align: center;
            font-size: 9pt;
            vertical-align: middle;
        }

        .tabla-servicio .celda-texto {
            text-align: left;
            font-size: 9pt;
            font-weight: 400;
            padding-left: 6px;
        }

        .tabla-servicio .celda-check {
            text-align: center;
            font-size: 9pt;
        }

        /* ========================================== */
        /* FIRMAS - CON NOMBRE DEL JEFE INMEDIATO */
        /* ========================================== */
        .firmas-container {
            margin-top: 15px;
            display: table;
            width: 100%;
        }

        .firmas-container .firma-box {
            display: table-cell;
            width: 60%;
            text-align: center;
            vertical-align: bottom;
            padding-right: 20px;
        }

        .firmas-container .firma-box .firma-linea {
            border-top: 1.5px solid #000000;
            margin: 25px 0 4px;
            width: 100%;
        }

        .firmas-container .firma-box .firma-nombre {
            font-family: 'Arial Black', 'Arial', sans-serif;
            font-size: 9pt;
            font-weight: 900;
            color: #000000;
        }

        .firmas-container .firma-box .firma-nombre-variable {
            font-family: 'Arial', sans-serif;
            font-size: 10pt;
            font-weight: 700;
            color: #003399;
            margin-bottom: 5px;
        }

        .firmas-container .sello-box {
            display: table-cell;
            width: 40%;
            text-align: center;
            vertical-align: bottom;
        }

        .firmas-container .sello-box .sello-linea {
            border-top: 1.5px solid #000000;
            margin: 25px 0 4px;
            width: 100%;
        }

        .firmas-container .sello-box .sello-nombre {
            font-family: 'Arial Black', 'Arial', sans-serif;
            font-size: 9pt;
            font-weight: 900;
            color: #000000;
        }

        /* ========================================== */
        /* PIE DE PÁGINA */
        /* ========================================== */
        .footer {
            margin-top: 15px;
            padding-top: 0px;
            font-family: 'Arial', sans-serif;
            font-size: 6.5pt;
            color: #333333;
            line-height: 1.6;
        }

        .footer .direccion {
            font-size: 6.5pt;
            color: #000000;
            line-height: 1.5;
            text-align: left;
            margin-top: 0px;
            padding-top: 0px;
        }

        .footer .telefono {
            font-size: 6.5pt;
            color: #000000;
            margin-top: 1px;
            text-align: left;
        }

        .footer .telefono a {
            color: #003366;
            text-decoration: underline;
        }

        .footer .fila-final {
            margin-top: 2px;
            width: 100%;
            display: table;
        }

        .footer .fila-final .email {
            display: table-cell;
            text-align: left;
            font-size: 6.5pt;
            color: #000000;
        }

        .footer .fila-final .email a {
            color: #003366;
            text-decoration: underline;
        }

        .footer .fila-final .certificado-wrapper {
            display: table-cell;
            text-align: right;
            vertical-align: middle;
            white-space: nowrap;
        }

        .footer .fila-final .certificado-wrapper img {
            height: 14px;
            width: auto;
            vertical-align: middle;
            margin-right: 3px;
        }

        .footer .fila-final .certificado-wrapper .certificado-text {
            font-family: 'Arial Black', 'Arial', sans-serif;
            font-size: 6pt;
            font-weight: 900;
            color: #000000;
            letter-spacing: 0.3px;
            vertical-align: middle;
        }

        /* ========================================== */
        /* IMPRESIÓN */
        /* ========================================== */
        @media print {
            body { background: #fff; }
            .tabla-competencias th { background-color: #f0f0f0; }
            .tabla-servicio th { background-color: #f0f0f0; }
        }

        .page-content {
            page-break-after: avoid;
            page-break-inside: avoid;
        }
    </style>
</head>
<body>

<div class="page-content">

    <!-- ========================================== -->
    <!-- ENCABEZADO -->
    <!-- ========================================== -->
    <div class="header">
        <div class="header-logo-cell">
            <img src="{{ $logo_base64 }}" alt="CONALEP">
        </div>
        <div class="header-text-cell">
            <div class="header-title">Colegio de Educación Profesional Técnica del Estado de Quintana Roo.</div>
            <div class="header-subtitle">ORGANISMO PÚBLICO DESCENTRALIZADO</div>
            <div class="header-plantel">Plantel Cancún II</div>
        </div>
    </div>

    <!-- ========================================== -->
    <!-- TÍTULO - EVALUACIÓN DE COMPETENCIAS -->
    <!-- ========================================== -->
    <div class="titulo-evaluacion">EVALUACIÓN DE COMPETENCIAS DEL DESEMPEÑO DEL ESTUDIANTE</div>

    <!-- ========================================== -->
    <!-- PÁRRAFO INTRODUCTORIO -->
    <!-- ========================================== -->
    <div class="parrafo-intro">
        Esta evaluación nos permite mejorar las competencias de estudiante durante su formación integral. Este formato debe ser llenado al finalizar el servicio social y/o prácticas profesionales.
    </div>

    <!-- ========================================== -->
    <!-- CHECKBOXES - SS Y PP - CENTRADOS -->
    <!-- ========================================== -->
    <div class="checkboxes-row">
        <span class="checkbox-label">SERVICIO SOCIAL</span>
        <span class="checkbox-inline">{{ $checkbox_ss }}</span>
        <span class="checkbox-separator"></span>
        <span class="checkbox-label">PRÁCTICAS PROFESIONALES</span>
        <span class="checkbox-inline">{{ $checkbox_pp }}</span>
    </div>

    <!-- ========================================== -->
    <!-- TABLA DE DATOS DEL ALUMNO -->
    <!-- ========================================== -->
    <table class="tabla-datos">
        <tr>
            <td class="label">Nombre del alumno:</td>
            <td class="value" colspan="3">{{ $nombre_completo }}</td>
        </tr>
        <tr>
            <td class="label">Carrera:</td>
            <td class="value" style="width:35%;">{{ $carrera }}</td>
            <td class="label" style="width:15%;">Fecha:</td>
            <td class="value" style="width:28%;">{{ $fecha_finalizacion }}</td>
        </tr>
        <tr>
            <td class="label">Nombre de la empresa:</td>
            <td class="value" colspan="3">{{ $empresa }}</td>
        </tr>
        <tr>
            <td class="label">Área asignada:</td>
            <td class="value" colspan="3">{{ $area_asignada }}</td>
        </tr>
    </table>

    <!-- ========================================== -->
    <!-- INSTRUCCIÓN -->
    <!-- ========================================== -->
    <div class="instruccion">
        Instrucción: Lea cada uno de los ítems y señale con la X la opción que mejor describa el desempeño y comportamiento del estudiante
    </div>

    <!-- ========================================== -->
    <!-- TABLA DE COMPETENCIAS -->
    <!-- ========================================== -->
    <table class="tabla-competencias">
        <thead>
            <tr>
                <th style="width:38%;">Comunicación y desarrollo profesional</th>
                <th style="width:15.5%;">EXCELENTE</th>
                <th style="width:15.5%;">BIEN</th>
                <th style="width:15.5%;">REGULAR</th>
                <th style="width:15.5%;">DEFICIENTE</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td class="celda-texto">Es puntual con su hora de llegada</td>
                <td class="celda-check"></td>
                <td class="celda-check"></td>
                <td class="celda-check"></td>
                <td class="celda-check"></td>
            </tr>
            <tr>
                <td class="celda-texto">Su presentación personal es</td>
                <td class="celda-check"></td>
                <td class="celda-check"></td>
                <td class="celda-check"></td>
                <td class="celda-check"></td>
            </tr>
            <tr>
                <td class="celda-texto">Tiene fluidez y facilidad en la comunicación oral</td>
                <td class="celda-check"></td>
                <td class="celda-check"></td>
                <td class="celda-check"></td>
                <td class="celda-check"></td>
            </tr>
            <tr>
                <td class="celda-texto">Facilidad para relacionar con los demás compañeros</td>
                <td class="celda-check"></td>
                <td class="celda-check"></td>
                <td class="celda-check"></td>
                <td class="celda-check"></td>
            </tr>
            <tr>
                <td class="celda-texto">Se adapta y respeta las políticas de la empresa/institución</td>
                <td class="celda-check"></td>
                <td class="celda-check"></td>
                <td class="celda-check"></td>
                <td class="celda-check"></td>
            </tr>
            <tr>
                <td class="celda-texto">Demuestra disponibilidad para aprender y colaborar</td>
                <td class="celda-check"></td>
                <td class="celda-check"></td>
                <td class="celda-check"></td>
                <td class="celda-check"></td>
            </tr>
            <tr>
                <td class="celda-texto">Demuestra iniciativa para solucionar problemas</td>
                <td class="celda-check"></td>
                <td class="celda-check"></td>
                <td class="celda-check"></td>
                <td class="celda-check"></td>
            </tr>
            <tr>
                <td class="celda-texto">Posee conocimientos indispensables de su formación profesional técnica</td>
                <td class="celda-check"></td>
                <td class="celda-check"></td>
                <td class="celda-check"></td>
                <td class="celda-check"></td>
            </tr>
            <tr>
                <td class="celda-texto">Demuestra compromiso en el desarrollo de sus actividades encomendadas</td>
                <td class="celda-check"></td>
                <td class="celda-check"></td>
                <td class="celda-check"></td>
                <td class="celda-check"></td>
            </tr>
            <tr>
                <td class="celda-texto">Fomenta el prestigio y buena imagen del plantel</td>
                <td class="celda-check"></td>
                <td class="celda-check"></td>
                <td class="celda-check"></td>
                <td class="celda-check"></td>
            </tr>
            <tr class="fila-conocimientos">
                <td class="celda-texto">Que conocimientos o habilidades considera que debe desarrollar o fortalecer el estudiante</td>
                <td colspan="4" class="celda-linea">
                    <div class="linea"></div>
                </td>
            </tr>
        </tbody>
    </table>

    <!-- ========================================== -->
    <!-- TÍTULO - EVALUACIÓN DEL SERVICIO RECIBIDO -->
    <!-- ========================================== -->
    <div class="titulo-servicio">EVALUACIÓN DEL SERVICIO RECIBIDO POR PARTE DEL COLEGIO</div>

    <!-- ========================================== -->
    <!-- TABLA DE EVALUACIÓN DEL SERVICIO -->
    <!-- ========================================== -->
    <table class="tabla-servicio">
        <thead>
            <tr>
                <th style="width:55%;">ASPECTO A EVALUAR</th>
                <th style="width:15%;">SI</th>
                <th style="width:15%;">NO</th>
                <th style="width:15%;">NO APLICA</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td class="celda-texto">El Colegio se contactó con usted para dar seguimiento al desempeño del estudiante</td>
                <td class="celda-check"></td>
                <td class="celda-check"></td>
                <td class="celda-check"></td>
            </tr>
            <tr>
                <td class="celda-texto">Considera que el estudiante posee el perfil para incorporarse al sector productivo</td>
                <td class="celda-check"></td>
                <td class="celda-check"></td>
                <td class="celda-check"></td>
            </tr>
        </tbody>
    </table>

    <!-- ========================================== -->
    <!-- FIRMAS - CON NOMBRE DEL JEFE INMEDIATO -->
    <!-- ========================================== -->
    <div class="firmas-container">
        <div class="firma-box">
            <div class="firma-nombre-variable">{{ $grado_academico_jefe }} {{ $nombre_jefe_inmediato }}</div>
            <div class="firma-linea"></div>
            <div class="firma-nombre">Nombre y firma del Jefe Inmediato</div>
        </div>
        <div class="sello-box">
            <div class="sello-linea"></div>
            <div class="sello-nombre">Sello</div>
        </div>
    </div>

    <!-- ========================================== -->
    <!-- PIE DE PÁGINA -->
    <!-- ========================================== -->
    <div class="footer">
        <div class="direccion">
            Región 228, Mza 5, Lote 1, Av. 20 de Noviembre<br>
            Entre costa maya y calle 61, Zona 4<br>
            Benito Juárez, Cancún, Quintana Roo, CP. 77516
        </div>
        <div class="telefono">
            Teléfono y Fax (01 998) 2710194
        </div>
        <div class="fila-final">
            <span class="email">
                e-mail: <a href="mailto:conalep_cancun2@yahoo.com.mx">conalep_cancun2@yahoo.com.mx</a>
            </span>
            <span class="certificado-wrapper">
                <img src="{{ $logo_pie_base64 }}" alt="Logo">
                <span class="certificado-text">Certificado conforme a los requisitos de la norma ISO 9001:2008</span>
            </span>
        </div>
    </div>

</div>

</body>
</html>