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
        Tecnologías
    </h3>
    <div class="container mx-auto">

        <div class="w-1/2">
            @if (session('success'))
                <x-alert id="alert-1" class="mt-2 bg-green-600">
                    {{ session('success') }}
                </x-alert>
            @endif

            @if (session('error'))
                <x-alert id="alert-1" class="mt-2 bg-red-600">
                    {{ session('error') }}
                </x-alert>
            @endif
        </div>

        <div class="flex">
            <a href="{{ route('tecnologia.create') }}"
                class="inline-flex items-center mx-1 px-4 mb-2 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                Nueva tecnología
            </a>

            <form action="/reporte-background-excel" method="POST">
                @csrf <!-- Token de protección contra CSRF -->
                <input type="hidden" name="estado" value="ACTIVO"> <!-- Parámetro enviado -->
                <button type="submit"
                    class="inline-flex items-center mx-1 px-4 mb-2 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 focus:bg-green-700 active:bg-green-900 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition ease-in-out duration-150">
                    <i class="fa-regular fa-file-excel"></i>Reporte Excel Background Todos
                </button>
            </form>

            <button onclick="generarReportePdfPaginado()"
                class="inline-flex items-center mx-1 px-4 mb-2 py-2 bg-red-700 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700 focus:bg-red-700 active:bg-red-900 focus:outline-none focus:ring-2 focus:ring-gray-200 focus:ring-offset-2 transition ease-in-out duration-150"
                type="button">
                <i class="fa-regular fa-file-pdf"></i>Reporte PDF - Screen
            </button>

            <button data-dialog-target="modal" onclick="generarReporteBackground()"
                class="inline-flex items-center mx-1 px-4 mb-2 py-2 bg-red-700 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700 focus:bg-red-700 active:bg-red-900 focus:outline-none focus:ring-2 focus:ring-gray-200 focus:ring-offset-2 transition ease-in-out duration-150"
                type="button">
                <i class="fa-regular fa-file-pdf"></i>Reporte PDF - Background
            </button>
        </div>

        <table id="example" class="display " style="width:100%">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>NOMBRE</th>
                    <th>DESCRIPCION</th>
                    <th>ESTADO</th>
                    <th>DESCARGAR</th>
                    <th></th>
                    <th></th>
                </tr>
            </thead>
            {{-- <tfoot>
                <tr>
                    <th>ID</th>
                    <th>NOMBRE</th>
                    <th>DESCRIPCION</th>
                    <th>ESTADO</th>
                    <th>DESCARGAR</th>
                    <th></th>
                    <th></th>
                </tr>
            </tfoot> --}}
        </table>

        <!-- Progress Bar -->
        <div data-dialog-backdrop="modal" data-dialog-backdrop-close="true" id="modal"
            class="pointer-events-none fixed inset-0 z-[999] grid h-screen w-screen place-items-center bg-black bg-opacity-60 opacity-0 backdrop-blur-sm transition-opacity duration-300">
            <div data-dialog="modal" class="relative m-4 p-4 w-2/5 min-w-[40%] max-w-[40%] rounded-lg bg-white shadow-sm">
                <div class="flex shrink-0 items-center pb-4 text-xl font-medium text-slate-800">
                    Generando reporte
                </div>
                <div class="relative border-t border-slate-200 py-4 leading-normal text-slate-600 font-light">
                    <div
                        class="flex w-full h-4 overflow-hidden font-sans text-xs font-medium rounded-full flex-start bg-blue-gray-50">
                        <div id="ProgressBarSize"
                            class="flex items-center justify-center h-full overflow-hidden text-white break-all bg-gray-900 rounded-full" style="width: 5%">1 %
                        </div>
                    </div>
                </div>
                <div class="flex shrink-0 flex-wrap items-center pt-4 justify-end">
                    <button data-dialog-close="true"
                        class="rounded-md border border-transparent py-2 px-4 text-center text-sm transition-all text-slate-600 hover:bg-slate-100 focus:bg-slate-100 active:bg-slate-100 disabled:pointer-events-none disabled:opacity-50 disabled:shadow-none"
                        type="button">
                        Cerrar
                    </button>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script src="https://code.jquery.com/jquery-3.7.1.js"></script>
        <script src="https://cdn.datatables.net/2.2.1/js/dataTables.js"></script>
        <script>
            //Variables para paginación
            let skip = 0;
            let take = 0;
            let searchValue = '';
            let orderColumn = '';
            let orderDescAsc = '';
            let isSubscribed = false;

            $(document).ready(function() {
                let table = new DataTable('#example', {
                    search: {
                        return: true
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

                        let url = `{{ url('/laravel-datatables-filter') }}?skip=${skip}&take=${take}&draw=${draw}&search=${searchValue}&orderColumn=${orderColumn}&order=${orderDescAsc}`;

                        $.ajax({
                            url: url,
                            type: 'GET',
                            dataType: 'json',
                            success: function(response) {
                                // console.log("Respuesta del servidor:", response);
                                callback(response); // Enviar datos a DataTables
                                $('#example_processing').hide(); // Ocultando loader
                                document.getElementById('loading').style.display = 'none';
                            },
                            error: function(xhr, status, error) {
                                console.error("Error en AJAX:", error);
                                $('#example_processing').hide(); // Ocultar loader si hay error
                                document.getElementById('loading').style.display = 'none';
                            }
                        });
                    },
                    language: {
                        sProcessing: "Procesando...",
                        sLengthMenu: "Mostrar _MENU_ registros por página",
                        sZeroRecords: "No se encontraron resultados",
                        sEmptyTable: "Ningún dato disponible en esta tabla",
                        // sInfo: "Mostrando _START_ a _END_ de _TOTAL_ entradas (filtradas de _MAX_ entradas)",
                        sInfo: "Mostrando _START_ a _END_ de _TOTAL_ ",
                        sInfoEmpty: "Mostrando 0 a 0 de 0 entradas",
                        sInfoFiltered: "(filtrado de _MAX_ entradas totales)",
                        sSearch: "Buscar:",
                        // oPaginate: {
                        //     sFirst: "Primero",
                        //     sPrevious: "Anterior",
                        //     sNext: "Siguiente",
                        //     sLast: "Último"
                        // }
                    },
                    order: [[0, 'desc']],
                    columns: [
                        { data: 'id' },
                        { data: 'nombre' },
                        { data: 'descripcion' },
                        { data: 'estado' },
                        {
                            data: null,
                            className: 'dt-center',
                            render: function(data, type, row) {
                                if (data.pdf) {
                                    return `<a href="${data.pdf}" target="_blank" class="text-red-700">
                                        <i class="fa-regular fa-file-pdf"></i>
                                    </a>`;
                                }
                                return '';
                            },
                            orderable: false
                        },
                        {
                            data: null,
                            className: 'dt-center',
                            render: function(data, type, row) {
                                let url = `{{ url('/tecnologias/${data.id}') }}`;
                                return `<a href="${url}" class="text-green-700" title="Editar Registro">
                                    <i class="fa fa-pencil"></i>
                                </a>`;
                            },
                            orderable: false
                        },
                        {
                            data: null,
                            className: 'dt-center',
                            render: function(data, type, row) {
                                let url = `{{ url('/tecnologias/${data.id}') }}`;
                                return `<form action="${url}" method="POST"
                                    style="display: inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-700 p-0 mx-2"
                                        title="Eliminar Registro">
                                        <i class="fa-regular fa-trash-can"></i>
                                    </button>
                                </form>`;
                            },
                            orderable: false
                        }
                    ]
                });
            });

            /**
             * Metodo que genera reporte en pdf en background tomando los datos de paginación del datatable
             */
            function generarReporteBackground() {
                document.getElementById('loading').style.display = 'flex';
                fetch(`/generar-pdf-background?skip=${skip}&take=${take}&search=${searchValue}&orderColumn=${orderColumn}&order=${orderDescAsc}`)
                    .then(response => response.json())
                    .then(dataGet => {
                        document.getElementById('loading').style.display = 'none';
                        // console.log(dataGet.data);

                        let ProgressBarSize = document.getElementById('ProgressBarSize');
                        ProgressBarSize.style.width = '5%';
                        ProgressBarSize.innerHTML = '1 %'

                        document.getElementById('modal').classList.remove('pointer-events-none', 'opacity-0');

                        if (!isSubscribed) {
                            window.Echo.channel('channel-name').listen('JobProgressUpdated', function(data) {
                                // console.log(data);

                                ProgressBarSize.style.width = data.progress + '%';
                                ProgressBarSize.innerHTML = data.progress + '%'

                                if (data.path) {
                                    window.open(data.path, '_blank');
                                    // ProgressBarSize.style.width = '0%';
                                    // ProgressBarSize.innerHTML = '5%';
                                    // document.getElementById('modal').classList.add('pointer-events-none', 'opacity-0');

                                }
                            });
                            isSubscribed = true;
                        }

                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('Error al generar el reporte');
                        document.getElementById('loading').style.display = 'none';
                    });

                // return;
            }

            function generarReportePdfPaginado(){
                document.getElementById('loading').style.display = 'flex';
                fetch(`/generar-pdf?skip=${skip}&take=${take}&search=${searchValue}&orderColumn=${orderColumn}&order=${orderDescAsc}`)
                    .then(response => response.blob())
                    .then(blob  => {
                        // console.log(blob );
                        let url = window.URL.createObjectURL(blob);
                        let a = document.createElement("a");
                        a.href = url;
                        a.download = "TECNOLOGIAS.pdf"; // Nombre del archivo
                        document.body.appendChild(a);
                        a.click();
                        a.remove();
                        document.getElementById('loading').style.display = 'none';
                    })
                    .catch(error => {
                        console.error("Error descargando el PDF:", error);
                        document.getElementById('loading').style.display = 'none';
                    });
            }
        </script>
    @endpush
</x-app-layout>
