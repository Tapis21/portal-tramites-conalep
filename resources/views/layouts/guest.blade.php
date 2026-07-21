<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'CONALEP') }} - {{ $title ?? 'Acceso' }}</title>

    <!-- Favicon CONALEP -->
    <link rel="icon" type="image/x-icon" href="{{ asset('images/286-Cancún-II.ico') }}">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:300,400,500,600,700,800&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-['Inter'] antialiased min-h-screen overflow-x-hidden text-gray-800">

    <!-- ========================================== -->
    <!-- FONDO DINÁMICO (GRADIENTE + PARTÍCULAS + WATERMARK) -->
    <!-- ========================================== -->
    <div class="fixed inset-0 pointer-events-none overflow-hidden z-0 bg-gradient-to-br from-green-50 via-white to-green-50/30 animate-gradient"></div>

    <!-- Partículas flotantes (bólitas + estrellitas) -->
    <div class="fixed inset-0 pointer-events-none overflow-hidden z-0">
        <!-- Bólitas verdosas (6) -->
        <div class="absolute w-16 h-16 md:w-20 md:h-20 rounded-full bg-green-700/15 left-[5%] top-[10%] animate-[float_20s_ease-in-out_infinite]"></div>
        <div class="absolute w-24 h-24 md:w-28 md:h-28 rounded-full bg-green-600/12 left-[85%] top-[15%] animate-[float_20s_ease-in-out_infinite] -delay-[4s]"></div>
        <div class="absolute w-12 h-12 md:w-16 md:h-16 rounded-full bg-emerald-500/10 left-[45%] top-[5%] animate-[float_20s_ease-in-out_infinite] -delay-[8s]"></div>
        <div class="absolute w-20 h-20 md:w-24 md:h-24 rounded-full bg-green-800/10 left-[20%] top-[75%] animate-[float_20s_ease-in-out_infinite] -delay-[12s]"></div>
        <div class="absolute w-14 h-14 md:w-18 md:h-18 rounded-full bg-emerald-400/8 left-[75%] top-[80%] animate-[float_20s_ease-in-out_infinite] -delay-[16s]"></div>
        <div class="absolute w-10 h-10 md:w-12 md:h-12 rounded-full bg-green-700/20 left-[55%] top-[90%] animate-[float_20s_ease-in-out_infinite] -delay-[20s]"></div>

        <!-- Estrellitas (6) -->
        <div class="absolute w-8 h-8 md:w-10 md:h-10 bg-amber-400/20 left-[15%] top-[35%] animate-[float_20s_ease-in-out_infinite] -delay-[2s] star-shape"></div>
        <div class="absolute w-10 h-10 md:w-12 md:h-12 bg-yellow-300/15 left-[90%] top-[45%] animate-[float_20s_ease-in-out_infinite] -delay-[6s] star-shape"></div>
        <div class="absolute w-6 h-6 md:w-8 md:h-8 bg-amber-300/25 left-[30%] top-[55%] animate-[float_20s_ease-in-out_infinite] -delay-[10s] star-shape"></div>
        <div class="absolute w-12 h-12 md:w-14 md:h-14 bg-yellow-400/10 left-[70%] top-[25%] animate-[float_20s_ease-in-out_infinite] -delay-[14s] star-shape"></div>
        <div class="absolute w-7 h-7 md:w-9 md:h-9 bg-amber-500/15 left-[10%] top-[65%] animate-[float_20s_ease-in-out_infinite] -delay-[18s] star-shape"></div>
        <div class="absolute w-9 h-9 md:w-11 md:h-11 bg-yellow-200/20 left-[60%] top-[40%] animate-[float_20s_ease-in-out_infinite] -delay-[22s] star-shape"></div>
    </div>

    <!-- Watermark en cascada (logo CONALEP .ico) -->
    <div class="fixed inset-0 pointer-events-none overflow-hidden z-0 flex items-center justify-center opacity-[0.06] select-none">
        <div class="grid grid-cols-6 sm:grid-cols-8 md:grid-cols-12 gap-3 md:gap-4 rotate-[-12deg] scale-125 w-full h-full p-4 md:p-8">
            @for($i = 0; $i < 144; $i++)
                <img src="{{ asset('images/286-Cancún-II.ico') }}" alt="" class="w-8 h-8 sm:w-10 sm:h-10 md:w-12 md:h-12 object-contain opacity-70">
            @endfor
        </div>
    </div>

    <!-- ========================================== -->
    <!-- CONTENIDO PRINCIPAL (Centrado) -->
    <!-- ========================================== -->
    <div class="relative z-10 min-h-screen flex flex-col items-center justify-center px-3 sm:px-6 py-6 sm:py-8">
        {{ $slot }}
    </div>

    <!-- ========================================== -->
    <!-- ANIMACIONES PERSONALIZADAS -->
    <!-- ========================================== -->
    <style>
        .animate-gradient { background-size: 400% 400%; animation: gradientShift 15s ease infinite; }
        @keyframes gradientShift { 0% { background-position: 0% 50%; } 50% { background-position: 100% 50%; } 100% { background-position: 0% 50%; } }
        @keyframes float { 0%,100% { transform: translateY(0) rotate(0deg) scale(1); } 33% { transform: translateY(-30px) rotate(120deg) scale(1.1); } 66% { transform: translateY(20px) rotate(240deg) scale(0.9); } }
        @keyframes pulseRing { 0%,100% { transform: scale(1); opacity: 1; } 50% { transform: scale(1.05); opacity: 0.5; } }
        @keyframes slideUp { to { opacity: 1; transform: translateY(0); } }
        @keyframes underlineGrow { to { transform: scaleX(1); } }
        .animate-\[float_20s_ease-in-out_infinite\] { animation: float 20s ease-in-out infinite; }
        .animate-\[pulseRing_3s_ease-in-out_infinite\] { animation: pulseRing 3s ease-in-out infinite; }
        .animate-\[slideUp_0\.8s_cubic-bezier\(0\.16\,1\,0\.3\,1\)_forwards\] { animation: slideUp 0.8s cubic-bezier(0.16,1,0.3,1) forwards; }
        .animate-\[underlineGrow_0\.8s_0\.8s_ease-out_forwards\] { animation: underlineGrow 0.8s 0.8s ease-out forwards; }
        .group-hover\:scale-x-100 { transform: scaleX(1); }
        .group:hover .absolute { transform: scaleX(1); }
        .transition-colors { transition-property: color, background-color, border-color, text-decoration-color, fill, stroke; transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1); transition-duration: 300ms; }
        .star-shape { clip-path: polygon(50% 0%, 61% 35%, 98% 35%, 68% 57%, 79% 91%, 50% 70%, 21% 91%, 32% 57%, 2% 35%, 39% 35%); background: radial-gradient(circle, rgba(255,200,0,0.25), rgba(255,200,0,0.05)); backdrop-filter: blur(1px); }
        @media (max-width: 480px) { .xs\:inline { display: none; } }
        @media (min-width: 481px) { .xs\:inline { display: inline; } }
        .grid-cols-6 { grid-template-columns: repeat(6, minmax(0, 1fr)); }
        @media (min-width: 640px) { .grid-cols-6 { grid-template-columns: repeat(8, minmax(0, 1fr)); } }
        @media (min-width: 768px) { .grid-cols-6 { grid-template-columns: repeat(12, minmax(0, 1fr)); } }
    </style>
</body>
</html>