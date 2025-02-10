import './bootstrap';

import Alpine from 'alpinejs';
import Swal from 'sweetalert2';

window.Alpine = Alpine;

// Crear una función global para usar fácilmente en cualquier parte
window.Swal = Swal;


Alpine.start();
