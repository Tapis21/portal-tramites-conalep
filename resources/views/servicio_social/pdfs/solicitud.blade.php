<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Solicitud de {{ $tipo_tramite }}</title>
    <style>
        /* ========================================== */
        /* CONFIGURACIÓN DE PÁGINA */
        /* ========================================== */
        @page {
            size: letter;
            margin: 8mm 15mm 8mm 15mm;
        }

        body {
            font-family: 'Arial MT', 'Arial', sans-serif;
            font-size: 10pt;
            line-height: 1.5;
            color: #000000;
            background: #ffffff;
        }

        /* ========================================== */
        /* ENCABEZADO - SIN LÍNEA NEGRA */
        /* ========================================== */
        .header {
            width: 100%;
            padding-bottom: 5px;
            margin-bottom: 5px;
            display: table;
        }

        .header-logo-cell {
            display: table-cell;
            width: 65px;
            vertical-align: middle;
            padding-right: 8px;
        }

        .header-logo-cell img {
            width: 60px;
            height: auto;
            display: block;
        }

        .header-text-cell {
            display: table-cell;
            vertical-align: middle;
        }

        .header-title {
            font-family: 'Arial Black', 'Arial', sans-serif;
            font-size: 6pt;
            font-weight: 900;
            color: #000000;
            letter-spacing: 0.3px;
            margin: 0;
            text-transform: uppercase;
            line-height: 1.3;
        }

        .header-subtitle {
            font-family: 'Arial MT', 'Arial', sans-serif;
            font-size: 6pt;
            color: #000000;
            font-weight: 600;
            letter-spacing: 0.5px;
            text-transform: uppercase;
            margin: 2px 0 0;
        }

        .header-plantel {
            font-family: 'Arial MT', 'Arial', sans-serif;
            font-size: 6pt;
            color: #000000;
            font-weight: 600;
            margin-top: 2px;
        }

        /* ========================================== */
        /* JEFATURA - Arial 8pt */
        /* ========================================== */
        .jefatura {
            text-align: center;
            font-family: 'Arial', sans-serif;
            font-size: 8pt;
            font-weight: 700;
            color: #000000;
            margin: 3px 0 5px;
            letter-spacing: 0.5px;
        }

        /* ========================================== */
        /* TÍTULO PRINCIPAL - Arial 12pt */
        /* ========================================== */
        .title-main {
            text-align: center;
            font-family: 'Arial', sans-serif;
            font-size: 12pt;
            font-weight: 700;
            color: #000000;
            margin: 4px 0 3px;
            letter-spacing: 0.3px;
        }

        .title-checkboxes {
            text-align: center;
            font-family: 'Arial', sans-serif;
            font-size: 12pt;
            font-weight: 700;
            color: #000000;
            margin: 2px 0 5px;
            letter-spacing: 0.3px;
        }

        .checkbox-inline {
            display: inline-block;
            border: 1.5px solid #000000;
            width: 14px;
            height: 14px;
            text-align: center;
            line-height: 14px;
            font-size: 10pt;
            font-weight: 700;
            margin: 0 4px;
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
        /* LEER IMPORTANTE - SIN PALO NEGRO Y SIN FONDO */
        /* ========================================== */
        .leer-box {
            padding: 5px 0px;
            margin: 4px 0 6px 0px;
            font-family: 'Arial MT', 'Arial', sans-serif;
            font-size: 10pt;
            text-align: justify;
            line-height: 1.6;
            color: #000000;
        }

        .leer-box strong {
            font-family: 'Arial', sans-serif;
            font-weight: 700;
            color: #000000;
        }

        /* ========================================== */
        /* SECCIONES - SIN LÍNEA GRIS */
        /* ========================================== */
        .section-title {
            font-family: 'Arial', sans-serif;
            font-size: 10pt;
            font-weight: 700;
            color: #000000;
            padding-bottom: 2px;
            margin: 7px 0 4px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        /* ========================================== */
        /* DATOS - Arial (VALORES EN AZUL CON ARIAL) */
        /* ========================================== */
        .field-row {
            margin: 2.5px 0;
            font-family: 'Arial MT', 'Arial', sans-serif;
            font-size: 10pt;
            color: #000000;
            padding-left: 0px;
        }

        .field-label {
            font-family: 'Arial', sans-serif;
            font-weight: 700;
            color: #000000;
        }

        /* 🔥 VALORES EN AZUL CON ARIAL (sin Arial MT) */
        .field-value {
            font-family: 'Arial', sans-serif;
            color: #003399;
            font-weight: 500;
            border-bottom: 1px dashed #aaaaaa;
            padding: 0 4px;
            font-size: 10pt;
        }

        .field-value-sin-linea {
            font-family: 'Arial', sans-serif;
            color: #003399;
            font-weight: 500;
            padding: 0 4px;
            font-size: 10pt;
        }

        .field-separator {
            display: inline-block;
            width: 20px;
        }

        .field-separator-lg {
            display: inline-block;
            width: 40px;
        }

        .field-separator-xl {
            display: inline-block;
            width: 60px;
        }

        /* ========================================== */
        /* FECHAS - Arial (VALORES EN AZUL CON ARIAL) */
        /* ========================================== */
        .fecha-row {
            margin: 2.5px 0;
            font-family: 'Arial MT', 'Arial', sans-serif;
            font-size: 10pt;
            color: #000000;
            padding-left: 0px;
        }

        .fecha-label {
            font-family: 'Arial', sans-serif;
            font-weight: 700;
            color: #000000;
        }

        /* 🔥 VALORES EN AZUL CON ARIAL (sin Arial MT) */
        .fecha-value {
            font-family: 'Arial', sans-serif;
            color: #003399;
            font-weight: 500;
            border-bottom: 1px dashed #aaaaaa;
            padding: 0 4px;
            font-size: 10pt;
        }

        .fecha-value-sin-linea {
            font-family: 'Arial', sans-serif;
            color: #003399;
            font-weight: 500;
            padding: 0 4px;
            font-size: 10pt;
        }

        .fecha-separator {
            display: inline-block;
            width: 15px;
        }

        .fecha-text-normal {
            font-family: 'Arial MT', 'Arial', sans-serif;
            font-size: 10pt;
            color: #000000;
        }

        /* ========================================== */
        /* LEER IMPORTANTE 2 - SIN PALO NEGRO Y SIN FONDO */
        /* ========================================== */
        .leer-box-2 {
            padding: 5px 0px;
            margin: 7px 0 5px 0px;
            font-family: 'Arial MT', 'Arial', sans-serif;
            font-size: 10pt;
            text-align: justify;
            line-height: 1.6;
            color: #000000;
        }

        .leer-box-2 strong {
            font-family: 'Arial', sans-serif;
            font-weight: 700;
            color: #000000;
        }

        /* ========================================== */
        /* DATOS INSTITUCIÓN - VERTICAL */
        /* ========================================== */
        .datos-vertical {
            margin: 2.5px 0;
            font-family: 'Arial MT', 'Arial', sans-serif;
            font-size: 10pt;
            color: #000000;
            padding-left: 0px;
        }

        .datos-vertical .label {
            font-family: 'Arial', sans-serif;
            font-weight: 700;
            color: #000000;
            display: inline-block;
            min-width: 180px;
        }

        /* 🔥 VALORES EN AZUL CON ARIAL (sin Arial MT) */
        .datos-vertical .value {
            font-family: 'Arial', sans-serif;
            color: #003399;
            font-weight: 500;
            border-bottom: 1px dashed #aaaaaa;
            padding: 0 4px;
            font-size: 10pt;
        }

        .datos-vertical .value-sin-linea {
            font-family: 'Arial', sans-serif;
            color: #003399;
            font-weight: 500;
            padding: 0 4px;
            font-size: 10pt;
        }

        /* ========================================== */
        /* FIRMAS - 3 COLUMNAS CON TABLA */
        /* ========================================== */
        .signature-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }

        .signature-table td {
            width: 33.33%;
            text-align: center;
            vertical-align: bottom;
            padding: 0 10px;
        }

        .signature-table .linea-firma {
            border-top: 1.5px solid #000000;
            margin: 50px 0 12px;
            width: 100%;
        }

        .signature-table .sin-linea {
            margin: 28px 0 4px;
            width: 100%;
        }

        .signature-name {
            font-family: 'Times New Roman', 'Times', serif;
            font-size: 11pt;
            font-weight: 700;
            color: #000000;
            white-space: nowrap;
        }

        .signature-title {
            font-family: 'Arial MT', 'Arial', sans-serif;
            font-size: 8pt;
            color: #444444;
            margin-top: 2px;
        }

        /* ========================================== */
        /* PIE DE PÁGINA */
        /* ========================================== */
        .footer {
            margin-top: 28px;
            padding-top: 0px;
            font-family: 'Arial MT', 'Arial', sans-serif;
            font-size: 7pt;
            color: #333333;
            line-height: 1.7;
        }

        .footer .direccion {
            font-size: 7pt;
            color: #000000;
            line-height: 1.6;
            text-align: left;
            margin-top: 0px;
            padding-top: 0px;
        }

        .footer .telefono {
            font-size: 7pt;
            color: #000000;
            margin-top: 2px;
            text-align: left;
        }

        .footer .telefono a {
            color: #003366;
            text-decoration: underline;
        }

        .footer .fila-final {
            margin-top: 3px;
            width: 100%;
            display: table;
        }

        .footer .fila-final .email {
            display: table-cell;
            text-align: left;
            font-size: 7pt;
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
            height: 16px;
            width: auto;
            vertical-align: middle;
            margin-left: 4px;
        }

        .footer .fila-final .certificado-wrapper .certificado-text {
            font-family: 'Arial Black', 'Arial', sans-serif;
            font-size: 6.5pt;
            font-weight: 900;
            color: #000000;
            letter-spacing: 0.5px;
            vertical-align: middle;
        }

        /* ========================================== */
        /* ESPACIADO PARA FIRMAS */
        /* ========================================== */
        .espacio-firmas {
            height: 20px;
        }

        /* ========================================== */
        /* IMPRESIÓN */
        /* ========================================== */
        @media print {
            body { background: #fff; }
        }

        /* ========================================== */
        /* EVITA QUE EL CONTENIDO SE ROMPA */
        /* ========================================== */
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
    <!-- JEFATURA -->
    <!-- ========================================== -->
    <div class="jefatura">JEFATURA DE PROYECTO DE PROMOCIÓN Y VINCULACIÓN</div>

    <!-- ========================================== -->
    <!-- TÍTULO -->
    <!-- ========================================== -->
    <div class="title-main">SOLICITUD DE INSCRIPCIÓN AL PROGRAMA DE</div>

    <div class="title-checkboxes">
        <span class="checkbox-label">SERVICIO SOCIAL</span>
        <span class="checkbox-inline">{{ $checkbox_ss }}</span>
        <span class="checkbox-separator"></span>
        <span class="checkbox-label">PRÁCTICAS PROFESIONALES</span>
        <span class="checkbox-inline">{{ $checkbox_pp }}</span>
    </div>

    <!-- ========================================== -->
    <!-- LEER IMPORTANTE 1 - SIN PALO NEGRO Y SIN PADDING -->
    <!-- ========================================== -->
    <div class="leer-box">
        <strong>LEER, IMPORTANTE: </strong>
        De acuerdo al título cuarto y del Capítulo IV art.135 al 141 y capitulo V art. 142 al 149 de las Reglas de convivencia Escolar del Sistema Nacional de Colegios de Educación Profesional Técnica se presenta la siguiente solicitud, el cual debe ser debidamente llenada por el estudiante, firmada y sellada por la institución o empresa que reciba al estudiante para que el colegio le extienda posteriormente la carta de presentación.
    </div>

    <!-- ========================================== -->
    <!-- DATOS DEL ESTUDIANTE -->
    <!-- ========================================== -->
    <div class="section-title">DATOS DEL ESTUDIANTE</div>

    <div class="field-row">
        <span class="field-label">Nombre:</span>
        <span class="field-value">{{ $nombre_completo }}</span>
        <span class="field-separator-xl"></span>
        <span class="field-label">Grupo:</span>
        <span class="field-value">{{ $grupo }}</span>
        <span class="field-separator"></span>
        <span class="field-label">Matrícula:</span>
        <span class="field-value">{{ $matricula }}</span>
        <span class="field-separator"></span>
        <span class="field-label">Turno:</span>
        <span class="field-value">{{ $turno }}</span>
        <span class="field-separator"></span>
        <span class="field-label">Semestre:</span>
        <span class="field-value">{{ $semestre }}°</span>
        <span class="field-separator"></span>
        <span class="field-label">Carrera:</span>
        <span class="field-value">{{ $carrera }}</span>
        <span class="field-separator"></span>
        <span class="field-label">Generación:</span>
        <span class="field-value">{{ $generacion }}</span>
    </div>

    <!-- ========================================== -->
    <!-- FECHA DE INICIO Y TÉRMINO -->
    <!-- ========================================== -->
    <div class="section-title">FECHA DE INICIO Y TÉRMINO</div>

    <div class="fecha-row">
        <span class="fecha-label">Del día</span>
        <span class="fecha-value">{{ $fecha_inicio }}</span>
        <span class="fecha-separator"></span>
        <span class="fecha-label">al día</span>
        <span class="fecha-value">{{ $fecha_finalizacion }}</span>
    </div>

    <div class="fecha-row">
        <span class="fecha-label">En el Horario de:</span>
        <span class="fecha-value">{{ $horario }}</span>
        <span class="fecha-separator"></span>
        <span class="fecha-text-normal">cubriendo 4 hrs. al día de Lunes a viernes con un total de horas</span>
    </div>

    <div class="fecha-row">
        <span class="fecha-label">SS:</span>
        <span class="fecha-value-sin-linea">480</span>
        <span class="checkbox-inline" style="width:13px; height:13px; line-height:13px; font-size:9pt;">{{ $checkbox_ss }}</span>
        <span class="fecha-separator"></span>
        <span class="fecha-label">PP:</span>
        <span class="fecha-value-sin-linea">360</span>
        <span class="checkbox-inline" style="width:13px; height:13px; line-height:13px; font-size:9pt;">{{ $checkbox_pp }}</span>
        <span class="fecha-separator"></span>
        <span class="fecha-text-normal">a lo largo de su servicio/prácticas</span>
    </div>

    <!-- ========================================== -->
    <!-- DATOS DE LA INSTITUCIÓN O EMPRESA - VERTICAL -->
    <!-- ========================================== -->
    <div class="section-title">DATOS DE LA INSTITUCIÓN O EMPRESA</div>

    <div class="datos-vertical">
        <span class="label">Grado académico, nombre completo y cargo de a quien irá dirigida la carta de presentación:</span>
    </div>
    <div class="datos-vertical" style="padding-left:30px; margin-top:2px;">
        <span class="value-sin-linea">{{ $grado_academico }} {{ $nombre_persona_carta }}</span>
        <span class="field-separator"></span>
        <span class="value-sin-linea">{{ $cargo_persona_carta }}</span>
    </div>

    <div class="datos-vertical" style="margin-top:5px;">
        <span class="label">Grado académico, nombre y cargo del jefe inmediato:</span>
    </div>
    <div class="datos-vertical" style="padding-left:30px; margin-top:2px;">
        <span class="value-sin-linea">{{ $grado_academico_jefe }} {{ $nombre_jefe_inmediato }}</span>
        <span class="field-separator"></span>
        <span class="value-sin-linea">{{ $cargo_jefe_inmediato }}</span>
    </div>

    <div class="datos-vertical" style="margin-top:5px;">
        <span class="label">Institución/Empresa:</span>
        <span class="value">{{ $empresa }}</span>
    </div>

    <div class="datos-vertical" style="margin-top:2px;">
        <span class="label">Área asignada:</span>
        <span class="value">{{ $area_asignada }}</span>
    </div>

    <div class="datos-vertical" style="margin-top:2px;">
        <span class="label">Apoyo al estudiante:</span>
        <span class="value">{{ $apoyo_estudiante }}</span>
    </div>

    <!-- ========================================== -->
    <!-- LEER IMPORTANTE 2 - SIN PALO NEGRO Y SIN PADDING -->
    <!-- ========================================== -->
    <div class="leer-box-2">
        <strong>LEER, IMPORTANTE: </strong>
        Con el fin de dar cumplimiento a los prescrito por la Ley Reglamentaria del Artículo 5º Constitucional, el suscrito acepta sujetarse al reglamento correspondiente y cumplir con el periodo manifestado, así como observar una conducta ejemplar durante su permanencia de lo contrario no le será extendida la constancia que lo acredite por la prestación de dicho servicio o práctica; igualmente, la institución o la empresa notificará al colegio los hechos en los que incurra el estudiante o cualquier evento a favor del mismo.
    </div>

    <!-- ========================================== -->
    <!-- ESPACIO PARA FIRMAS -->
    <!-- ========================================== -->
    <div class="espacio-firmas"></div>

    <!-- ========================================== -->
    <!-- FIRMAS - 3 COLUMNAS CON TABLA -->
    <!-- ========================================== -->
    <table class="signature-table">
        <tr>
            <td>
                <div class="linea-firma"></div>
                <div class="signature-name">NOMBRE Y FIRMA</div>
                <div class="signature-title">Jefe del área</div>
            </td>
            <td>
                <div class="sin-linea"></div>
                <div class="signature-name">SELLO</div>
            </td>
            <td>
                <div class="linea-firma"></div>
                <div class="signature-name">DR. NICOLAS CANO RAMÍREZ</div>
                <div class="signature-title">Director del CONALEP II</div>
            </td>
        </tr>
    </table>

    <!-- ========================================== -->
    <!-- PIE DE PÁGINA -->
    <!-- ========================================== -->
    <div class="footer">
        <div class="direccion">
            Región 228, Mza 5, Lote 1, Av. 20 de Nov.<br>
            Entre costa maya y calle 61, zona 4,<br>
            Cancún, Quintana Roo, CP. 77516
        </div>
        <div class="telefono">
            Teléfono y Fax (01 998) 2710194
        </div>
        <div class="fila-final">
            <span class="email">
                e-mail: <a href="mailto:vinculacion.cancun2@qroo.conalep.edu.mx">vinculacion.cancun2@qroo.conalep.edu.mx</a>
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