//Variables para paginación
let skip = 0;
let take = 0;
let searchValue = '';
let orderColumn = '';
let orderDescAsc = '';

let cargarDatosTecnologia = false;
let tableTecnologia;
let userId = null;
let arrIdsTecnologias = [];
let consultarTecnologiasUser = false;

$(document).ready(function() {
    let table = new DataTable('#userTable', {
        search: {
            return: false
        },
        processing: false,
        serverSide: true,
        ajax: function(data, callback, settings) {
            document.getElementById('loading').style.display = 'flex';

            skip = data.start;
            take = data.length;
            let draw = data.draw; // Capturar draw que envía DataTables
            let order = data.order; // Orden de las columnas
            searchValue = data.search.value; // Valor de la búsqueda
            let column = order[0]?.column;

            orderColumn = data?.columns[column]?.data ?? '';
            orderDescAsc = order[0]?.dir ?? '';

            let url = `/users?skip=${skip}&take=${take}&draw=${draw}&search=${searchValue}&orderColumn=${orderColumn}&order=${orderDescAsc}`;

            $.ajax({
                url: url,
                type: 'GET',
                dataType: 'json',
                success: function(response) {
                    callback(response); // Envian datos a DataTables
                    document.getElementById('loading').style.display = 'none';
                },
                error: function(xhr, status, error) {
                    console.error("Error en AJAX:", error);
                    document.getElementById('loading').style.display = 'none';
                }
            });
        },
        language: {
            sProcessing: "Procesando...",
            sLengthMenu: "Mostrar _MENU_ Registros por página",
            sZeroRecords: "No se encontraron resultados",
            sEmptyTable: "Ningún dato disponible en esta tabla",
            sInfo: "Mostrando _START_ a _END_ de _TOTAL_ ",
            sInfoEmpty: "Mostrando 0 a 0 de 0 entradas",
            sInfoFiltered: "(filtrado de _MAX_ entradas totales)",
            sSearch: "Buscar:",
        },
        order: [[0, 'desc']],
        columns: [
            { data: 'name' },
            { data: 'email' },
            {
                data: null,
                className: 'dt-center',
                render: function(data, type, row) {
                    return `
                        <button class="open-modal text-green-600" data-user-id="${row.id}" data-user-name="${row.name}" title="Asignar Tecnologías">
                            <i class="fa-solid fa-gears"></i>
                        </button>
                    `;
                },
                orderable: false
            }
        ]
    });
});

$(document).on('click', '.open-modal', function() {
    consultarTecnologiasUser = true;
    let nombreUsuario = $(this).data('user-name');
    $('#titleModal').text(`TECNOLOGIAS - ${nombreUsuario}`);
    userId = $(this).data('user-id');

    $('#modal').removeClass('hidden').addClass('opacity-100 pointer-events-auto');
    tableTecnologia.settings()[0].oFeatures.bServerSide = true;  // Vuelve a habilitar el server-side
    cargarDatosTecnologia = true;
    tableTecnologia.ajax.reload();
});

$('#cerrarModal').on('click', function() {
    $('#modal').addClass('hidden').removeClass('opacity-100 pointer-events-auto');
    cargarDatosTecnologia = false;
    tableTecnologia.settings()[0].oFeatures.bServerSide = false;  // Desactiva temporalmente el server-side
    tableTecnologia.clear().draw();  // Vacía la tabla
    arrIdsTecnologias = [];
});

// ================== DATATABLE TECNOLOGIAS =================================== //

// Variables para paginación tecnologias
let skipTecnologia          = 0;
let takeTecnologia          = 0;
let searchValueTecnologia   = '';
let orderColumnTecnologia   = '';
let orderDescAscTecnologia  = '';

$(document).ready(function() {
    tableTecnologia = new DataTable('#tecnologiasTable', {
        search: { return: true },
        processing: false,
        serverSide: false,
        ajax: async function(data, callback, settings) {
            if (cargarDatosTecnologia){

                await almacenarAsignados();

                document.getElementById('loading').style.display = 'flex';
                skipTecnologia = data.start;
                takeTecnologia = data.length;
                let draw_ = data.draw;
                let order = data.order;
                searchValueTecnologia = data.search.value;
                let column = order[0]?.column;

                orderColumnTecnologia = data?.columns[column]?.data ?? '';
                orderDescAscTecnologia = order[0]?.dir ?? '';

                let url = `/laravel-datatables-filter?skip=${skipTecnologia}&take=${takeTecnologia}&draw=${draw_}&search=${searchValueTecnologia}&orderColumn=${orderColumnTecnologia}&order=${orderDescAscTecnologia}`;
                $.ajax({
                    url: url,
                    type: 'GET',
                    dataType: 'json',
                    success: async function(response) {
                        callback(response);
                        if (consultarTecnologiasUser) {
                            await consultarTecnologiasAsignadasUser();
                            consultarTecnologiasUser = false;
                        }
                        await seleccionarTecnologias();
                        document.getElementById('loading').style.display = 'none';
                    },
                    error: function(xhr, status, error) {
                        console.error("Error en AJAX:", error);
                        document.getElementById('loading').style.display = 'none';
                        arrIdsTecnologias = [];
                    }
                });
            }
        },
        language: {
            sProcessing: "Procesando...",
            sLengthMenu: "Mostrar _MENU_ registros por página",
            sZeroRecords: "No se encontraron resultados",
            sEmptyTable: "Ningún dato disponible en esta tabla",
            sInfo: "Mostrando _START_ a _END_ de _TOTAL_ ",
            sInfoEmpty: "Mostrando 0 a 0 de 0 entradas",
            sInfoFiltered: "(filtrado de _MAX_ entradas totales)",
            sSearch: "Buscar:",
        },
        order: [[1, 'desc']],
        columns: [
            {
                data: null,
                render: DataTable.render.select(),
                orderable: false
            },
            { data: 'id' },
            { data: 'nombre' },
            { data: 'estado' }
        ],
        select: {
            style: 'multi',
            selector: 'td:first-child',
            headerCheckbox: 'select-page'
        }
    });
});

/**
 * Consulto tecnologias ya asignadas para marcarlas en el datatable
 */
async function consultarTecnologiasAsignadasUser() {
    try {
        const respuesta = await fetch(`/tecnologias-user/${userId}`);

        if (!respuesta.ok) {
            throw new Error('Error al obtener los usuarios');
        }
        const datos = await respuesta.json();
        datos.data.tecnologias.forEach(tecnologia => {
            arrIdsTecnologias.push(tecnologia.id);
        });
    } catch (error) {
        console.error('Hubo un error:', error);
    }
}

/**
 * Selecciono tecnologias que estan en el array.
 */
async function seleccionarTecnologias(){
    tableTecnologia.rows().every(async function(rowIdx) {
        let data = this.data();
        if(arrIdsTecnologias.includes(data.id)){
            let rowNode = this.node();
            let select = $('td:first-child input', rowNode);

            if (select.length > 0) {
                select[0].checked = true; // Marcar checkbox
            }

            // Marcar la fila como seleccionada en DataTables
            this.select();
        }
    });
}

/**
 * Almaceno las tecnologias seleccionadas y elimino los duplicados.
 */
async function almacenarAsignados(){
    tableTecnologia.rows().every(async function(rowIdx) {
        let data = this.data();
        let rowNode = this.node();
        let select = $('td:first-child input', rowNode);
        if (select[0].checked) {
            arrIdsTecnologias.push(data.id);
        }else{
            arrIdsTecnologias = arrIdsTecnologias.filter(tecnologia => tecnologia !== data.id)
        }
    });
    arrIdsTecnologias = [...new Set(arrIdsTecnologias)];
}

/**
 * Asigno las tecnologias seleccionadas al usuario.
 */
async function asignar(){
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    document.getElementById('loading').style.display = 'flex';
    await almacenarAsignados();
    try {
        const respuesta = await fetch(`/asignar-tecnologia/${userId}`,
            {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: JSON.stringify({ tecnologias: arrIdsTecnologias }),
            }
        );

        if (!respuesta.ok) {
            throw new Error('Error al obtener los usuarios');
        }
        const datos = await respuesta.json();
        alert(datos.message);
        document.getElementById("cerrarModal").click();
        document.getElementById('loading').style.display = 'none';
    } catch (error) {
        document.getElementById('loading').style.display = 'none';
        console.error('Hubo un error:', error);
    }
}
