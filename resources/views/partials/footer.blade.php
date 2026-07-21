<!-- ========================================== -->
<!-- FOOTER REUTILIZABLE -->
<!-- ========================================== -->
<div class="mt-4 sm:mt-6 text-center border-t border-gray-200/50 pt-3 sm:pt-4">
    <div class="w-8 h-1 sm:w-10 bg-gradient-to-r from-green-700 to-amber-500 rounded-full mx-auto mb-2 sm:mb-3"></div>

    <!-- Derechos reservados con hover -->
    <span class="text-[10px] sm:text-xs text-gray-400 transition-colors duration-300 hover:text-green-700 cursor-pointer block">
        &copy; {{ date('Y') }} CONALEP. Todos los derechos reservados.
    </span>

    <!-- Créditos del desarrollador -->
    <div class="mt-1.5 sm:mt-2 flex flex-wrap items-center justify-center gap-x-2 gap-y-1 text-[10px] sm:text-[11px] text-gray-400">
        <span>Desarrollado por</span>
        <span class="font-medium text-gray-500 hover:text-green-700 transition-colors duration-300">Jesus Aron Tut Najera</span>
        <span class="text-gray-300">|</span>
        <a href="mailto:arontutnajera@gmail.com" class="hover:text-green-700 transition-colors duration-300 flex items-center gap-0.5">
            <span class="iconify w-3 h-3 sm:w-3.5 sm:h-3.5" data-icon="mdi:email-outline"></span>
            <span class="hidden xs:inline">arontutnajera@gmail.com</span>
        </a>
        <span class="text-gray-300">|</span>
        <a href="tel:9987339901" class="hover:text-green-700 transition-colors duration-300 flex items-center gap-0.5">
            <span class="iconify w-3 h-3 sm:w-3.5 sm:h-3.5" data-icon="mdi:phone-outline"></span>
            <span class="hidden xs:inline">998 733 9901</span>
        </a>
        <span class="text-gray-300">|</span>
        <span class="flex items-center gap-0.5 hover:text-green-700 transition-colors duration-300">
            <span class="iconify w-3 h-3 sm:w-3.5 sm:h-3.5" data-icon="mdi:school-outline"></span>
            <span class="hidden xs:inline">Estudié en UPQROO</span>
        </span>
    </div>

    <!-- Mensaje secreto (provisional) -->
    <p class="mt-1 text-[9px] sm:text-[10px] text-gray-300/70 italic hover:text-gray-500 transition-colors duration-300">
        * Este sistema se encuentra en fase de mejora continua.
    </p>
</div>