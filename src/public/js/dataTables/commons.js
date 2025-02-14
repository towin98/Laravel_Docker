// Variables para paginacion
let table = null
let pageInfo = null;
export let searchValue = '';
let order = null
export let orderColumn = null;
export let orderDescAsc = null;
export let skip = null;
export let take = null;

export const languageData = {
    "sProcessing": "Procesando...",
    "sLengthMenu": "Mostrar _MENU_ Registros por página",
    "sZeroRecords": "No se encontraron resultados",
    "sEmptyTable": "Ningún dato disponible en esta tabla",
    "sInfo": "Mostrando _START_ a _END_ de _TOTAL_ ",
    "sInfoEmpty": "Mostrando 0 a 0 de 0 entradas",
    "sInfoFiltered": "(filtrado de _MAX_ entradas totales)",
    "sSearch": "Buscar:"
};

export function parametrosPaginacion(idTable, columns) {
    table = $(idTable).DataTable();
    pageInfo = table.page.info(); // Obtener información de paginación

    searchValue = table.search(); // Valor de búsqueda
    order = table.order()[0]; // Ordenamiento
    let indiceColumna = table.column(order[0]).dataSrc(); // Nombre de la columna ordenada
    orderColumn = columns[indiceColumna].name;
    orderDescAsc = order[1]; // 'asc' o 'desc'

    skip = pageInfo.start; // Desde qué registro
    take = pageInfo.length; // Cuántos registros por página
};

export const datePickerlanguageEs ={
    closeText: 'Cerrar',
    prevText: 'Anterior',
    nextText: 'Siguiente',
    currentText: 'Hoy',
    monthNames: ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio',
                'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'],
    monthNamesShort: ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun',
                    'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'],
    dayNames: ['Domingo', 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado'],
    dayNamesShort: ['Dom', 'Lun', 'Mar', 'Mié', 'Jue', 'Vie', 'Sáb'],
    dayNamesMin: ['Do', 'Lu', 'Ma', 'Mi', 'Ju', 'Vi', 'Sa'],
    weekHeader: 'Sm',
    dateFormat: 'yy-mm-dd',
    firstDay: 1,
    showMonthAfterYear: false,
};
