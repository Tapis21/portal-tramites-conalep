import './bootstrap';
import Alpine from 'alpinejs';
import '@iconify/iconify';
import flatpickr from 'flatpickr';
import 'flatpickr/dist/flatpickr.min.css';
import 'flatpickr/dist/themes/airbnb.css';
import '../css/componentes/flatpickr-conalep.css';

window.Alpine = Alpine;
window.flatpickr = flatpickr;

Alpine.start();

document.addEventListener('DOMContentLoaded', function() {
    const fechaInput = document.getElementById('fecha_inicio');
    if (fechaInput) {
        flatpickr(fechaInput, {
            locale: 'es',
            dateFormat: "Y-m-d",
            weekNumbers: true,
            firstDayOfWeek: 1, // LA SEMANA EMPIEZA EN LUNES
            disableMobile: true,
            disable: [
                function(date) {
                    return date.getDay() === 0 || date.getDay() === 6;
                }
            ],
            onChange: function(selectedDates, dateStr, instance) {
                const finalizacionInput = document.getElementById('fecha_finalizacion');
                if (finalizacionInput && selectedDates[0]) {
                    var fechaInicio = selectedDates[0];
                    var fechaFin = new Date(fechaInicio);
                    fechaFin.setMonth(fechaFin.getMonth() + 6);
                    
                    var dia = fechaFin.getDay();
                    if (dia === 6) {
                        fechaFin.setDate(fechaFin.getDate() + 2);
                    } else if (dia === 0) {
                        fechaFin.setDate(fechaFin.getDate() + 1);
                    }
                    
                    var año = fechaFin.getFullYear();
                    var mes = String(fechaFin.getMonth() + 1).padStart(2, '0');
                    var dia = String(fechaFin.getDate()).padStart(2, '0');
                    finalizacionInput.value = `${año}-${mes}-${dia}`;
                }
            }
        });
    }
});