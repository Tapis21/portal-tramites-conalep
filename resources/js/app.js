import './bootstrap';
import Alpine from 'alpinejs';
import '@iconify/iconify';
import flatpickr from 'flatpickr';
import 'flatpickr/dist/flatpickr.min.css';

window.Alpine = Alpine;
window.flatpickr = flatpickr;

Alpine.start();

// Inicializar Flatpickr cuando el DOM esté listo
document.addEventListener('DOMContentLoaded', function() {
    const fechaInput = document.getElementById('fecha_inicio');
    if (fechaInput) {
        flatpickr(fechaInput, {
            dateFormat: "Y-m-d",
            disable: [
                function(date) {
                    // Deshabilitar sábados y domingos
                    return date.getDay() === 0 || date.getDay() === 6;
                }
            ]
        });
    }
});