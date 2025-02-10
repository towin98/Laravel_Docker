<x-app-layout>
    @push('styles')
        <link rel="stylesheet" href="https://cdn.datatables.net/2.2.1/css/dataTables.dataTables.css">
        <style>
            label .dt-input{
                width: 64px !important;
            }
        </style>
    @endpush
    <div class="container mx-auto lg:w-2/4 flex flex-col rounded-lg bg-white shadow-sm p-2 my-6 border border-slate-200">
        <h3 class="text-center my-3 font-semibold leading-snug tracking-normal text-slate-800 mx-auto w-full text-lg max-w-md lg:max-w-xl lg:text-2xl">
            Usuarios
        </h3>
        <div class="overflow-x-auto">
            <table id="userTable" class="display " style="width:100%">
                <thead>
                    <tr>
                        <th>Nombre</th>
                        <th>E-mail</th>
                        <th>Asignar Tecnologías</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>

    <div id="modal" class="pointer-events-none fixed inset-0 z-[999] grid h-screen w-screen place-items-center bg-black bg-opacity-60 opacity-0 backdrop-blur-sm transition-opacity duration-300">
        <div class="relative m-4 p-4 lg:w-2/3 w-full rounded-lg bg-white shadow-sm overflow-x-auto">
            <div id="titleModal" class="flex justify-center shrink-0 items-center pb-4 text-xl font-medium text-slate-800"></div>
            <div class="relative border-t border-slate-200 py-4 leading-normal text-slate-600 font-light overflow-x-auto" style="height: 70vh;">
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
                <button id="asignar" class="rounded-md bg-green-600 py-2 px-4 border border-transparent text-center text-sm text-white transition-all shadow-md hover:bg-green-700 ml-2">Asignar</button>
            </div>
        </div>
    </div>

    @push('scripts')
    <script src="{{ asset('js/dataTables/jquery-3.7.1.js') }}"></script>
    <script src="{{ asset('js/dataTables/datatables.js') }}"></script>
    <script src="{{ asset('js/dataTables/dataTables.select.js') }}"></script>
    <script src="{{ asset('js/dataTables/select.dataTables.js') }}"></script>
    <script type="module">
        import { languageData } from '/js/dataTables/commons.js';

        //evento click en asignar
        document.addEventListener("DOMContentLoaded", function() {
            const btnAsignar = document.getElementById('asignar');
            btnAsignar.addEventListener('click', asignar);
        });


        let cargarDatosTecnologia = false;
        let tableTecnologia;
        let userId = null;
        let arrIdsTecnologias = [];
        let consultarTecnologiasUser = false;

        const columns = [
            { name: 'name' },
            { name: 'email' },
            { name: 'asignar', orderable: false, searchable: false},
        ];

        $(document).ready(function() {
            let table = new DataTable('#userTable', {
                search: {
                    return: false
                },
                processing: false,
                serverSide: true,
                ajax: {
                    url: "{{ route('users.database') }}",
                    dataSrc: function (json) {
                        document.getElementById('loading').style.display = 'none'; // Ocultar el loading cuando los datos se cargan
                        return json.data;
                    },
                    beforeSend: function () {
                        document.getElementById('loading').style.display = 'flex';  // Mostrar el loading antes de que se realice la solicitud
                    },
                },
                language: languageData,
                order: [[0, 'desc']],
                columns: columns
            });
        });

        $(document).on('click', '.open-modal', async function() {
            document.getElementById('loading').style.display = 'flex';
            let nombreUsuario = $(this).data('user-name');
            $('#titleModal').text(`TECNOLOGIAS - ${nombreUsuario}`);
            userId = $(this).data('user-id');

            $('#modal').removeClass('pointer-events-none').addClass('opacity-100 pointer-events-auto');

            await consultarTecnologiasAsignadasUser();
            console.log("consultarTecnologiasUser");

            if (tableTecnologia) {
                tableTecnologia.settings()[0].oFeatures.bServerSide = true;  // Vuelve a habilitar el server-side
                tableTecnologia.ajax.reload();
            }

            if (!tableTecnologia) {
                tableTecnologia = new DataTable('#tecnologiasTable', {
                    processing: true,
                    serverSide: true,  // Ahora sí se activa correctamente
                    ajax: {
                        url: "{{ route('datatables.index') }}",
                        dataSrc: function (json) {
                            document.getElementById('loading').style.display = 'none';
                            return json.data;
                        },
                        beforeSend: async function () {
                            if (tableTecnologia) {
                                await almacenarAsignados();
                            }
                            document.getElementById('loading').style.display = 'flex';
                        },
                    },
                    columns: columnsTecnologias,
                    language: languageData,
                    order: [[1, 'desc']],
                    select: {
                        style: 'multi',
                        selector: 'td:first-child',
                        headerCheckbox: 'select-page'
                    }
                });
                tableTecnologia.on('draw', async function() {
                    // Evento 'draw' cuando la tabla termina de cargar o se cambia la paginación/búsqueda
                    await seleccionarTecnologias();
                    console.log('draw');
                });
            }
        });

        $('#cerrarModal').on('click', function() {
            $('#modal').addClass('pointer-events-none').removeClass('opacity-100 pointer-events-auto');
            tableTecnologia.settings()[0].oFeatures.bServerSide = false;  // Desactiva temporalmente el server-side
            tableTecnologia.clear().draw();
            arrIdsTecnologias = [];
        });

        //#### DATATABLES TECNOLOGIAS MODAL

        const columnsTecnologias = [
            {
                name: 'checkbox',
                render: DataTable.render.select(),
                orderable: false
            },
            { name: 'id' },
            { name: 'nombre' },
            { name: 'estado' }
        ]

    /**
     * Consulto tecnologias ya asignadas para marcarlas en el datatable
     */
    async function consultarTecnologiasAsignadasUser() {
        try {
            console.log(arrIdsTecnologias);
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
            let rowNode = this.node();
            let select = $('td:first-child input', rowNode);
            if(arrIdsTecnologias.includes(data[1])){

                if (select.length > 0) {
                    select[0].checked = true; // Marcar checkbox
                }

                // Marcar la fila como seleccionada en DataTables
                this.select();
            }else{
                if (select.length > 0) {
                    select[0].checked = false; // Desmarcar checkbox
                }
                // Desmarcar la fila como seleccionada en DataTables
                this.deselect();
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
                arrIdsTecnologias.push(data[1]);
            }else{
                arrIdsTecnologias = arrIdsTecnologias.filter(tecnologia => tecnologia !== data[1])
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

    </script>
    @endpush
</x-app-layout>
