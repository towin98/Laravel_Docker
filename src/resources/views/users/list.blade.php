<x-app-layout>
    @push('styles')
        <link rel="stylesheet" href="https://cdn.datatables.net/2.2.1/css/dataTables.dataTables.css">
        <style>
            label .dt-input{
                width: 64px !important;
            }
        </style>
    @endpush
    <h3 class="text-center my-3 font-semibold leading-snug tracking-normal text-slate-800 mx-auto w-full text-lg max-w-md lg:max-w-xl lg:text-2xl">
        Usuarios
    </h3>
    <div class="container mx-auto lg:w-2/4">
        <table id="userTable" class="display " style="width:100%">
            <thead>
                <tr>
                    <th>Nombre</th>
                    <th>E-mail</th>
                    <th></th>
                </tr>
            </thead>
        </table>
    </div>

    <div id="modal" class="pointer-events-none fixed inset-0 z-[999] grid h-screen w-screen place-items-center bg-black bg-opacity-60 opacity-0 backdrop-blur-sm transition-opacity duration-300">
        <div class="relative m-4 p-4 lg:w-2/3 w-full rounded-lg bg-white shadow-sm">
            <div id="titleModal" class="flex justify-center shrink-0 items-center pb-4 text-xl font-medium text-slate-800"></div>
            <div class="relative border-t border-slate-200 py-4 leading-normal text-slate-600 font-light" style="height: 70vh; overflow:scroll">
                <table id="tecnologiasTable" class="display " style="width:100%">
                    <thead>
                        <tr>
                            <th></th>
                            <th>Id</th>
                            <th>Nombre</th>
                            <th>Estado</th>
                        </tr>
                    </thead>
                </table>
            </div>
            <div class="flex shrink-0 flex-wrap items-center pt-4 justify-end">
                <button id="cerrarModal" class="rounded-md border border-transparent py-2 px-4 text-center text-sm transition-all text-slate-600 hover:bg-slate-100">Cerrar</button>
                <button onclick="asignar()" class="rounded-md bg-green-600 py-2 px-4 border border-transparent text-center text-sm text-white transition-all shadow-md hover:bg-green-700 ml-2">Asignar</button>
            </div>
        </div>
    </div>

    @push('scripts')
    <script src="https://code.jquery.com/jquery-3.7.1.js"></script>
    <script src="https://cdn.datatables.net/2.2.1/js/dataTables.js"></script>

    <script src="https://cdn.datatables.net/select/3.0.0/js/dataTables.select.js"></script>
    <script src="https://cdn.datatables.net/select/3.0.0/js/select.dataTables.js"></script>

    <script>
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

                    let url = `{{ url('/users') }}?skip=${skip}&take=${take}&draw=${draw}&search=${searchValue}&orderColumn=${orderColumn}&order=${orderDescAsc}`;

                    $.ajax({
                        url: url,
                        type: 'GET',
                        dataType: 'json',
                        success: function(response) {
                            callback(response); // Enviar datos a DataTables
                            $('#userTable_processing').hide(); // Ocultando loader
                            document.getElementById('loading').style.display = 'none';
                        },
                        error: function(xhr, status, error) {
                            console.error("Error en AJAX:", error);
                            $('#userTable_processing').hide(); // Ocultar loader si hay error
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
                                <button class="open-modal text-blue-500" data-user-id="${row.id}" data-user-name="${row.name}">
                                    <i class="fa-solid fa-person-harassing"></i>
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

        // Cerrar modal al hacer clic en el botón de cerrar
        $('#cerrarModal').on('click', function() {
            $('#modal').addClass('hidden').removeClass('opacity-100 pointer-events-auto');
            cargarDatosTecnologia = false;
            tableTecnologia.settings()[0].oFeatures.bServerSide = false;  // Desactiva temporalmente el server-side
            tableTecnologia.clear().draw();  // Vacía la tabla
            arrIdsTecnologias = [];
        });

        //----------------------------------------------------------------

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

                        let url = `{{ url('/laravel-datatables-filter') }}?skip=${skipTecnologia}&take=${takeTecnologia}&draw=${draw_}&search=${searchValueTecnologia}&orderColumn=${orderColumnTecnologia}&order=${orderDescAscTecnologia}`;
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
            document.getElementById('loading').style.display = 'flex';
            await almacenarAsignados();
            try {
                const respuesta = await fetch(`/asignar-tecnologia/${userId}`,
                    {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
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
    </script>
    @endpush
</x-app-layout>
