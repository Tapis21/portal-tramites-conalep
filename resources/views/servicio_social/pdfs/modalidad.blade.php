<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Modalidad - {{ $tipo_tramite }}</title>
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
        /* ENCABEZADO - SIN LÍNEA NEGRA */
        /* ========================================== */
        .header {
            width: 100%;
            padding-bottom: 5px;
            margin-bottom: 10px;
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
        /* FECHA - ALINEACIÓN DERECHA */
        /* ========================================== */
        .fecha-derecha {
            text-align: right;
            font-family: 'Arial MT', 'Arial', sans-serif;
            font-size: 10pt;
            color: #000000;
            margin-bottom: 25px;
        }

        /* ========================================== */
        /* DR. NICOLÁS CANO RAMÍREZ - IZQUIERDA */
        /* ========================================== */
        .destinatario-izquierda {
            text-align: left;
            font-family: 'Arial Black', 'Arial', sans-serif;
            font-size: 12pt;
            font-weight: 900;
            color: #000000;
            line-height: 1.8;
            margin-bottom: 30px;
        }

        /* ========================================== */
        /* AT'N: LIC. CARLOS ALBERTO... - DERECHA */
        /* ========================================== */
        .destinatario-derecha {
            text-align: right;
            font-family: 'Arial Black', 'Arial', sans-serif;
            font-size: 12pt;
            font-weight: 900;
            color: #000000;
            line-height: 1.8;
            margin-bottom: 25px;
            padding-left: 40px;
        }

        /* ========================================== */
        /* CUERPO DEL TEXTO */
        /* ========================================== */
        .cuerpo-texto {
            font-family: 'Arial MT', 'Arial', sans-serif;
            font-size: 10pt;
            text-align: justify;
            color: #000000;
            line-height: 1.8;
            margin-bottom: 10px;
        }

        .cuerpo-texto .variable-azul {
            color: #003399;
            font-weight: 500;
        }

        .cuerpo-texto .negrita {
            font-weight: 700;
            font-family: 'Arial', sans-serif;
        }

        /* ========================================== */
        /* OPCIONES */
        /* ========================================== */
        .opciones {
            font-family: 'Arial MT', 'Arial', sans-serif;
            font-size: 10pt;
            text-align: justify;
            color: #000000;
            line-height: 2.2;
            margin: 12px 0 14px 20px;
        }

        .opciones .checkbox {
            font-family: 'Arial', sans-serif;
            font-weight: 700;
        }

        .opciones .linea {
            display: inline-block;
            width: 150px;
            border-bottom: 1px solid #000000;
            margin-left: 5px;
        }

        .opciones .linea-larga {
            display: inline-block;
            width: 250px;
            border-bottom: 1px solid #000000;
            margin-left: 5px;
        }

        /* ========================================== */
        /* FIRMAS - CON MÁS ESPACIADO */
        /* ========================================== */
        .firmas {
            margin-top: 95px;
            display: table;
            width: 100%;
        }

        .firmas .firma-box {
            display: table-cell;
            width: 50%;
            text-align: center;
            vertical-align: bottom;
            padding: 0 20px;
        }

        .firmas .firma-linea {
            border-top: 1.5px solid #000000;
            margin: 45px 0 6px;
            width: 100%;
        }

        .firmas .firma-nombre {
            font-family: 'Arial Black', 'Arial', sans-serif;
            font-size: 12pt;
            font-weight: 900;
            color: #000000;
            padding-top: 4px;
        }

        /* ========================================== */
        /* PIE DE PÁGINA - COMPLETO */
        /* ========================================== */
        .footer {
            margin-top: 25px;
            padding-top: 6px;
            border-top: 1px solid #cccccc;
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
    <!-- FECHA - ALINEACIÓN DERECHA -->
    <!-- ========================================== -->
    <div class="fecha-derecha">
        Cancún, Quintana Roo a {{ \Carbon\Carbon::now()->translatedFormat('d') }} de {{ \Carbon\Carbon::now()->translatedFormat('F') }} del {{ \Carbon\Carbon::now()->format('Y') }}.
    </div>

    <!-- ========================================== -->
    <!-- DR. NICOLÁS CANO RAMÍREZ - IZQUIERDA -->
    <!-- ========================================== -->
    <div class="destinatario-izquierda">
        DR. NICOLÁS CANO RAMÍREZ<br>
        DIRECTOR DEL PLANTEL<br>
        CONALEP CANCÚN II
    </div>

    <!-- ========================================== -->
    <!-- AT'N: LIC. CARLOS ALBERTO... - DERECHA -->
    <!-- ========================================== -->
    <div class="destinatario-derecha">
        AT'N: LIC. CARLOS ALBERTO AZUETA SALAZAR<br>
        JEFE DE PROMOCIÓN Y VINCULACIÓN CONALEP<br>
        CANCÚN II
    </div>

    <!-- ========================================== -->
    <!-- CUERPO DEL TEXTO 1 -->
    <!-- ========================================== -->
    <div class="cuerpo-texto">
        Por medio de la presente me permito enviarle un cordial saludo; mi nombre es
        <span class="variable-azul">{{ $nombre_completo }}</span> del grupo
        <span class="variable-azul">{{ $grupo }}</span> de la carrera de P.T.B en
        <span class="variable-azul">{{ $carrera }}</span> con número de matrícula
        <span class="variable-azul">{{ $matricula }}</span>; y me dirijo a usted para hacer de su conocimiento que
        para cumplir con el trámite de <span class="negrita">{{ $tipo_tramite }}</span>, deseo participar en:
    </div>

    <!-- ========================================== -->
    <!-- OPCIONES -->
    <!-- ========================================== -->
    <div class="opciones">
        <div>(&nbsp;&nbsp;&nbsp;&nbsp;) Servicio Social en la dependencia: _________________________________</div>
        <div>(&nbsp;&nbsp;&nbsp;&nbsp;) Clubes académicos: <span class="linea-larga"></span></div>
        <div>(&nbsp;&nbsp;&nbsp;&nbsp;) Demostración de experiencia laboral (Anexar la constancia laboral)</div>
        <div>(&nbsp;&nbsp;&nbsp;&nbsp;) Selectivo: <span class="linea-larga"></span></div>
        <div>(&nbsp;&nbsp;&nbsp;&nbsp;) Otro: <span class="linea-larga"></span></div>
    </div>

    <!-- ========================================== -->
    <!-- CUERPO DEL TEXTO 2 -->
    <!-- ========================================== -->
    <div class="cuerpo-texto">
        Cabe mencionar que tanto yo como mi tutor de nombre
        <span class="variable-azul">{{ $nombre_tutor ?? '________________________' }}</span>
        <span class="negrita">(Anexo copia de su identificación oficial)</span> estamos enterados que el servicio social es un requisito del
        Proceso de Titulación, y que el plantel me ha brindado diversas opciones para poder
        cumplirlo, eligiendo de manera libre y consciente la más conveniente a mis intereses y
        necesidades personales y familiares.
    </div>

    <!-- ========================================== -->
    <!-- FIRMAS - CON MÁS ESPACIADO -->
    <!-- ========================================== -->
    <div class="firmas">
        <div class="firma-box">
            <div class="firma-linea"></div>
            <div class="firma-nombre">NOMBRE Y FIRMA DEL ALUMNO</div>
        </div>
        <div class="firma-box">
            <div class="firma-linea"></div>
            <div class="firma-nombre">NOMBRE Y FIRMA DEL TUTOR</div>
        </div>
    </div>

    <!-- ========================================== -->
    <!-- PIE DE PÁGINA - COMPLETO -->
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