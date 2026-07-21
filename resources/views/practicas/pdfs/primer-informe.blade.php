<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $tipo_tramite }} - 1er Informe</title>
    <style>
        /* ========================================== */
        /* CONFIGURACIÓN DE PÁGINA */
        /* ========================================== */
        @page {
            size: letter;
            margin: 10mm 15mm 8mm 15mm;
        }

        body {
            font-family: 'Arial MT', 'Arial', sans-serif;
            font-size: 10pt;
            line-height: 1.6;
            color: #000000;
            background: #ffffff;
        }

        /* ========================================== */
        /* ENCABEZADO - 2 LOGOS EN LA MISMA LÍNEA */
        /* ========================================== */
        .header {
            width: 100%;
            padding-bottom: -6px;
            margin-bottom: -6px;
            display: table;
            border-collapse: collapse;
        }

        .header .logo-cell {
            display: table-cell;
            vertical-align: middle;
            text-align: left;
        }

        .header .logo-educacion {
            height: 110px;
            width: auto;
            display: inline-block;
            vertical-align: middle;
            margin-right: 90px;
        }

        .header .logo-conalep {
            height: 50px;
            width: auto;
            display: inline-block;
            vertical-align: middle;
        }

        /* ========================================== */
        /* TÍTULO - INFORME DE ACTIVIDADES */
        /* ========================================== */
        .titulo-informe {
            text-align: center;
            font-family: 'Arial', sans-serif;
            font-size: 13pt;
            font-weight: 700;
            color: #000000;
            margin-top: -16px;
            margin-bottom: 0px;
            letter-spacing: 1px;
            text-transform: uppercase;
        }

        .subtitulo-informe {
            text-align: center;
            font-family: 'Arial', sans-serif;
            font-size: 11pt;
            font-weight: 700;
            color: #000000;
            margin-top: -8px;
            margin-bottom: 10px;
            letter-spacing: 0.5px;
            text-transform: uppercase;
        }

        /* ========================================== */
        /* FECHA - DERECHA */
        /* ========================================== */
        .fecha-derecha {
            text-align: right;
            font-family: 'Arial MT', 'Arial', sans-serif;
            font-size: 10pt;
            color: #000000;
            margin-bottom: 15px;
        }

        /* ========================================== */
        /* DATOS DEL ALUMNO */
        /* ========================================== */
        .datos-container {
            margin-left: 0px;
            margin-bottom: 5px;
        }

        .dato-row {
            font-family: 'Arial MT', 'Arial', sans-serif;
            margin-bottom: 4px;
        }

        .dato-row .label {
            font-family: 'Arial', sans-serif;
            font-size: 9.5pt;
            font-weight: 700;
            color: #000000;
            display: block;
        }

        .dato-row .value {
            font-family: 'Arial', sans-serif;
            font-size: 11pt;
            font-weight: 400;
            color: #003399;
            display: block;
            margin-top: 0px;
        }

        .dato-row .value-negro {
            font-family: 'Arial', sans-serif;
            font-size: 11pt;
            font-weight: 400;
            color: #000000;
            display: block;
            margin-top: 0px;
        }

        /* ========================================== */
        /* ACTIVIDADES REALIZADAS */
        /* ========================================== */
        .actividades-titulo {
            font-family: 'Arial', sans-serif;
            font-size: 10pt;
            font-weight: 700;
            color: #000000;
            margin-top: 5px;
            margin-bottom: -4px;
            text-transform: uppercase;
        }

        .actividades-lineas {
            margin-left: 0px;
            font-family: 'Arial MT', 'Arial', sans-serif;
            font-size: 10pt;
            color: #000000;
            line-height: 2.0;
        }

        .actividades-lineas .linea {
            display: block;
            border-bottom: 1px solid #000000;
            width: 100%;
            margin-bottom: 2px;
            height: 18px;
        }

        /* ========================================== */
        /* FIRMAS - CON ESPACIO PARA FIRMAR */
        /* ========================================== */
        .firmas-container {
            margin-top: 15px;
            width: 100%;
        }

        .firmas-container .firma-tabla {
            display: table;
            width: 100%;
        }

        .firmas-container .firma-col {
            display: table-cell;
            width: 42%;
            text-align: center;
            vertical-align: bottom;
        }

        .firmas-container .firma-col .firma-linea {
            border-top: 1.5px solid #000000;
            margin: 110px 0 8px;
            width: 100%;
        }

        .firmas-container .firma-col .firma-nombre {
            font-family: 'Arial Black', 'Arial', sans-serif;
            font-size: 8pt;
            font-weight: 900;
            color: #000000;
        }

        .firmas-container .firma-col .firma-titulo {
            font-family: 'Arial', sans-serif;
            font-size: 8.5pt;
            font-weight: 700;
            color: #000000;
            margin-bottom: 4px;
        }

        .firmas-container .firma-col .firma-nombre-variable {
            font-family: 'Arial', sans-serif;
            font-size: 9pt;
            font-weight: 700;
            color: #003399;
            margin-bottom: 15px;
        }

        .firmas-container .firma-col .firma-nombre-alumno {
            font-family: 'Arial', sans-serif;
            font-size: 9pt;
            font-weight: 700;
            color: #003399;
            margin-bottom: 15px;
        }

        .firmas-container .sello-col {
            display: table-cell;
            width: 16%;
            text-align: center;
            vertical-align: middle;
        }

        .firmas-container .sello-col .sello-texto {
            font-family: 'Arial Black', 'Arial', sans-serif;
            font-size: 11pt;
            font-weight: 900;
            color: #000000;
            letter-spacing: 2px;
        }

        /* ========================================== */
        /* PIE DE PÁGINA - CON ESPACIO */
        /* ========================================== */
        .footer {
            margin-top: 20px;
            padding-top: 6px;
            font-family: 'Arial MT', 'Arial', sans-serif;
            font-size: 7pt;
            color: #333333;
            line-height: 1.6;
            text-align: center;
        }

        .footer .ccp {
            font-size: 7pt;
            color: #000000;
            margin-top: 0px;
            text-align: center;
            font-weight: 600;
        }

        .footer .direccion {
            font-size: 7pt;
            color: #000000;
            line-height: 1.5;
            text-align: center;
        }

        .footer .correo {
            font-size: 7pt;
            color: #000000;
            margin-top: -2px;
            text-align: center;
        }

        .footer .correo a {
            color: #003366;
            text-decoration: underline;
        }

        /* ========================================== */
        /* IMPRESIÓN */
        /* ========================================== */
        @media print {
            body { background: #fff; }
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
        <div class="logo-cell">
            <img class="logo-educacion" src="{{ $logo_educacion_base64 }}" alt="Educación">
            <img class="logo-conalep" src="{{ $logo_conalep_base64 }}" alt="CONALEP">
        </div>
    </div>

    <!-- ========================================== -->
    <!-- TÍTULO -->
    <!-- ========================================== -->
    <div class="titulo-informe">1ER INFORME DE ACTIVIDADES</div>
    <div class="subtitulo-informe">{{ $tipo_tramite }}</div>

    <!-- ========================================== -->
    <!-- FECHA -->
    <!-- ========================================== -->
    <div class="fecha-derecha">
        Benito Juárez, Quintana Roo a {{ $fecha_primer_informe }}
    </div>

    <!-- ========================================== -->
    <!-- DATOS DEL ALUMNO -->
    <!-- ========================================== -->
    <div class="datos-container">
        <div class="dato-row">
            <span class="label">Nombre del Alumno:</span>
            <span class="value">{{ $nombre_completo }}</span>
        </div>

        <div class="dato-row">
            <span class="label">Plan de Estudios:</span>
            <span class="value">PT-B en {{ $carrera }}</span>
        </div>

        <div class="dato-row">
            <span class="label">Nombre del Programa:</span>
            <span class="value-negro">APOYO A LAS DIFERENTES AREAS</span>
        </div>

        <div class="dato-row">
            <span class="label">Nombre de la Empresa o Institución:</span>
            <span class="value">{{ $empresa }}</span>
        </div>

        <div class="dato-row">
            <span class="label">Periodo o número de horas que abarca el Informe:</span>
            <span class="value">{{ $tipo_tramite === 'Prácticas Profesionales' ? '180 horas' : '240 horas' }}</span>
        </div>
    </div>

    <!-- ========================================== -->
    <!-- ACTIVIDADES REALIZADAS -->
    <!-- ========================================== -->
    <div class="actividades-titulo">Actividades realizadas:</div>

    <div class="actividades-lineas">
        <div class="linea"></div>
        <div class="linea"></div>
        <div class="linea"></div>
        <div class="linea"></div>
        <div class="linea"></div>
        <div class="linea"></div>
        <div class="linea"></div>
        <div class="linea"></div>
        <div class="linea"></div>
        <div class="linea"></div>
        <div class="linea"></div>
        <div class="linea"></div>
    </div>

    <!-- ========================================== -->
    <!-- FIRMAS - CON NOMBRE DEL JEFE Y DEL ALUMNO -->
    <!-- ========================================== -->
    <div class="firmas-container">
        <div class="firma-tabla">
            <!-- Columna 1: Asesor de Servicio Social -->
            <div class="firma-col">
                <div class="firma-titulo">Asesor de Servicio Social</div>
                <div class="firma-nombre-variable">{{ $grado_academico_jefe }} {{ $nombre_jefe_inmediato }}</div>
                <div class="firma-linea"></div>
                <div class="firma-nombre">NOMBRE Y FIRMA DEL JEFE INMEDIATO</div>
            </div>

            <!-- Columna 2: SELLO -->
            <div class="sello-col">
                <div class="sello-texto">SELLO</div>
            </div>

            <!-- Columna 3: Prestador de Servicio Social -->
            <div class="firma-col">
                <div class="firma-titulo">Prestador de Servicio Social</div>
                <div class="firma-nombre-alumno">{{ $nombre_completo }}</div>
                <div class="firma-linea"></div>
                <div class="firma-nombre">NOMBRE Y FIRMA DEL ALUMNO</div>
            </div>
        </div>
    </div>

    <!-- ========================================== -->
    <!-- PIE DE PÁGINA -->
    <!-- ========================================== -->
    <div class="footer">
        <div class="ccp">C.c.p. Área de Vinculación.- Presente</div>
        <div class="direccion">
            REGIÓN 228, MANZANA 5, LOTE 1, AV. 20 DE NOV. POR 61. COL. REGIÓN 228, C.P. 77516. BENITO JUÁREZ, QUINTANA ROO
        </div>
        <div class="direccion" style="margin-top:0px;">
            Tel: 998 843 2231
        </div>
        <div class="correo">
            Correo: <a href="mailto:conalep_cancun2@yahoo.com.mx">conalep_cancun2@yahoo.com.mx</a>
        </div>
    </div>

</div>

</body>
</html>