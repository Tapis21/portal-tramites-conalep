<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Carta Presentación - {{ $tipo_tramite }}</title>
    <style>
        /* ========================================== */
        /* CONFIGURACIÓN DE PÁGINA */
        /* ========================================== */
        @page {
            size: letter;
            margin: 10mm 15mm 10mm 15mm;
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
            padding-bottom: 8px;
            margin-bottom: 12px;
            display: table;
            border-collapse: collapse;
        }

        .header .logo-cell {
            display: table-cell;
            vertical-align: middle;
            text-align: left;
        }

        .header .logo-educacion {
            height: 125px; /* 🔥 TU TAMAÑO */
            width: auto;
            display: inline-block;
            vertical-align: middle;
            margin-right: 110px; /* 🔥 TU ESPACIO ENTRE LOGOS */
        }

        .header .logo-conalep {
            height: 60px; /* 🔥 TU TAMAÑO */
            width: auto;
            display: inline-block;
            vertical-align: middle;
        }

        /* ========================================== */
        /* TÍTULO - CARTA PRESENTACIÓN */
        /* ========================================== */
        .titulo-carta {
            text-align: center;
            font-family: 'Arial', sans-serif;
            font-size: 12pt;
            font-weight: 700;
            color: #000000;
            margin-bottom: 15px;
            letter-spacing: 1px;
        }

        /* ========================================== */
        /* FECHA - DERECHA */
        /* ========================================== */
        .fecha-derecha {
            text-align: right;
            font-family: 'Arial MT', 'Arial', sans-serif;
            font-size: 10pt;
            color: #000000;
            margin-bottom: 20px;
        }

        /* ========================================== */
        /* DESTINATARIO - IZQUIERDA */
        /* ========================================== */
        .destinatario {
            text-align: left;
            font-family: 'Arial', sans-serif;
            font-size: 11pt;
            font-weight: 700;
            color: #000000;
            line-height: 1.8;
            margin-bottom: 25px;
        }

        /* ========================================== */
        /* CUERPO DEL TEXTO */
        /* ========================================== */
        .cuerpo-texto {
            font-family: 'Arial', sans-serif;
            font-size: 12pt;
            text-align: justify;
            color: #000000;
            line-height: 1.8;
            margin-bottom: 15px;
        }

        .cuerpo-texto .variable-azul {
            color: #003399;
            font-weight: 700;
        }

        .cuerpo-texto .negrita {
            font-weight: 700;
        }

        /* ========================================== */
        /* AGRADECIMIENTO */
        /* ========================================== */
        .agradecimiento {
            font-family: 'Arial', sans-serif;
            font-size: 12pt;
            text-align: justify;
            color: #000000;
            line-height: 1.8;
            margin-bottom: 80px;
        }

        /* ========================================== */
        /* ATENTAMENTE + FIRMA + SELLO */
        /* ========================================== */
        .firma-container {
            margin-top: 80px;
            display: table;
            width: 100%;
        }

        .firma-container .atentamente {
            display: table-cell;
            width: 60%;
            text-align: left;
            vertical-align: bottom;
        }

        .firma-container .atentamente .atentamente-texto {
            font-family: 'Arial', sans-serif;
            font-size: 12pt;
            font-weight: 700;
            color: #000000;
            margin-bottom: 10px;
        }

        .firma-container .atentamente .firma-img {
            display: block;
            margin-bottom: 5px;
        }

        .firma-container .atentamente .firma-img img {
            height: 100px;
            width: auto;
        }

        .firma-container .atentamente .firma-nombre {
            font-family: 'Arial Black', 'Arial', sans-serif;
            font-size: 11pt;
            font-weight: 900;
            color: #000000;
        }

        .firma-container .atentamente .firma-cargo {
            font-family: 'Arial MT', 'Arial', sans-serif;
            font-size: 10pt;
            color: #000000;
            font-weight: 600;
        }

        .firma-container .sello {
            display: table-cell;
            width: 40%;
            text-align: right;
            vertical-align: bottom;
            padding-right: 26px;
        }

        .firma-container .sello img {
            height: 115px;
            width: auto;
        }

        /* ========================================== */
        /* PIE DE PÁGINA */
        /* ========================================== */
        .footer {
            margin-top: 50px;
            padding-top: 8px;
            border-top: 1px solid #cccccc;
            font-family: 'Arial MT', 'Arial', sans-serif;
            font-size: 7.5pt;
            color: #333333;
            line-height: 1.7;
            text-align: center;
        }

        .footer .ccp {
            font-size: 7.5pt;
            color: #000000;
            margin-top: 2px;
            text-align: center;
            font-weight: 600;
        }

        .footer .direccion {
            font-size: 7.5pt;
            color: #000000;
            line-height: 1.6;
            text-align: center;
        }

        .footer .correo {
            font-size: 7.5pt;
            color: #000000;
            margin-top: 2px;
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
    <!-- ENCABEZADO - 2 LOGOS EN LA MISMA LÍNEA -->
    <!-- ========================================== -->
    <div class="header">
        <div class="logo-cell">
            <img class="logo-educacion" src="{{ $logo_educacion_base64 }}" alt="Educación">
            <img class="logo-conalep" src="{{ $logo_conalep_base64 }}" alt="CONALEP">
        </div>
    </div>

    <!-- ========================================== -->
    <!-- TÍTULO - CARTA DE PRESENTACIÓN -->
    <!-- ========================================== -->
    <div class="titulo-carta">CARTA DE PRESENTACIÓN</div>

    <!-- ========================================== -->
    <!-- FECHA - DERECHA -->
    <!-- ========================================== -->
    <div class="fecha-derecha">
        Benito Juárez, Quintana Roo a {{ \Carbon\Carbon::now()->translatedFormat('j') }} de {{ \Carbon\Carbon::now()->translatedFormat('F') }} del {{ \Carbon\Carbon::now()->format('Y') }}
    </div>

    <!-- ========================================== -->
    <!-- DESTINATARIO - IZQUIERDA -->
    <!-- ========================================== -->
    <div class="destinatario">
        {{ $grado_academico }} {{ $nombre_persona_carta }}<br>
        {{ $cargo_persona_carta }}<br>
        {{ $empresa }}<br>
        PRESENTE
    </div>

    <!-- ========================================== -->
    <!-- CUERPO DEL TEXTO - SEGÚN TRÁMITE -->
    <!-- ========================================== -->

    @if($tipo_tramite === 'Prácticas Profesionales')
    <!-- TEXTO PARA PRÁCTICAS PROFESIONALES -->
    <div class="cuerpo-texto">
        Por medio de la presente me permito presentar a sus finas atenciones al alumno (a) 
        <span class="variable-azul">{{ $nombre_completo }}</span>; de la carrera de Profesional Técnico Bachiller en 
        <span class="variable-azul">{{ $carrera }}</span> con matrícula 
        <span class="variable-azul">{{ $matricula }}</span> quien reúne los requisitos institucionales establecidos para realizar sus 
        <span class="negrita">PRÁCTICAS PROFESIONALES</span>, y se interesa en llevarlo a cabo en esa Unidad Administrativa a su digno cargo por el periodo comprendido del 
        <span class="variable-azul">{{ $fecha_inicio }}</span> al 
        <span class="variable-azul">{{ $fecha_finalizacion }}</span>, en un horario de 
        <span class="variable-azul">{{ $horario }}</span> hrs., hasta completar las 
        <span class="variable-azul">360</span> horas requeridas.
    </div>

    <div class="agradecimiento">
        Agradezco de antemano las facilidades brindadas a los alumnos que prestan <span class="negrita">PRÁCTICAS PROFESIONALES</span>.
    </div>
    @else
    <!-- TEXTO PARA SERVICIO SOCIAL -->
    <div class="cuerpo-texto">
        Por medio de la presente me permito presentar a sus finas atenciones al alumno (a) 
        <span class="variable-azul">{{ $nombre_completo }}</span>; de la carrera de Profesional Técnico Bachiller en 
        <span class="variable-azul">{{ $carrera }}</span> con matrícula 
        <span class="variable-azul">{{ $matricula }}</span> quien reúne los requisitos institucionales establecidos para realizar su 
        <span class="negrita">SERVICIO SOCIAL</span>, y se interesa en llevarlo a cabo en esa Unidad Administrativa a su digno cargo por el periodo comprendido del 
        <span class="variable-azul">{{ $fecha_inicio }}</span> al 
        <span class="variable-azul">{{ $fecha_finalizacion }}</span>, en un horario de 
        <span class="variable-azul">{{ $horario }}</span> hrs., hasta completar las 
        <span class="variable-azul">480</span> horas requeridas.
    </div>

    <div class="agradecimiento">
        Agradezco de antemano las facilidades brindadas a los alumnos que prestan <span class="negrita">SERVICIO SOCIAL</span>.
    </div>
    @endif

    <!-- ========================================== -->
    <!-- ATENTAMENTE + FIRMA + SELLO -->
    <!-- ========================================== -->
    <div class="firma-container">
        <div class="atentamente">
            <div class="atentamente-texto">ATENTAMENTE</div>
            <div class="firma-img">
                <img src="{{ $firma_dr_base64 }}" alt="Firma Dr. Cano">
            </div>
            <div class="firma-nombre">DR. NICOLÁS CANO RAMÍREZ</div>
            <div class="firma-cargo">DIRECTOR DEL PLANTEL CONALEP CANCÚN II</div>
        </div>
        <div class="sello">
            <img src="{{ $sello_conalep_base64 }}" alt="Sello CONALEP">
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
        <div class="direccion" style="margin-top:1px;">
            Tel: 998 140 5030
        </div>
        <div class="correo">
            Correo: <a href="mailto:vinculacion.cancun2@qroo.conalep.edu.mx">vinculacion.cancun2@qroo.conalep.edu.mx</a>
        </div>
    </div>

</div>

</body>
</html>