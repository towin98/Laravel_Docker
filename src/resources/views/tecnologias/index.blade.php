<x-app-layout>
    @push('styles')
    <link rel="stylesheet" href="https://cdn.datatables.net/2.2.1/css/dataTables.dataTables.css">
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.14.1/themes/base/jquery-ui.css">

    <style>
        .dt-length select{
            width: 64px !important;
        }

        /* Agregando estilos al modal de datapicker */
        .ui-datepicker select.ui-datepicker-year {
            width: 48%;
            padding-top: 0px;
            padding-bottom: 0px;
        }

        .ui-datepicker select.ui-datepicker-month {
            padding-top: 0px;
            padding-bottom: 0px;
        }
        /* fin estilos datepicker */
    </style>
    @endpush
    <div class="container mx-auto flex flex-col rounded-lg bg-white shadow-sm p-2 my-6 border border-slate-200">

        <h3 class="text-center mb-3 mt-1 font-semibold leading-snug tracking-normal text-slate-800 mx-auto w-full text-lg max-w-md lg:max-w-xl lg:text-2xl">
            Tecnologías
        </h3>

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

        <div class="flex overflow-x-auto py-2">
            <a href="{{ route('tecnologia.create') }}"
                class="inline-flex items-center mx-1 px-4 mb-2 py-2 bg-blue-500 border rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 transition ease-in-out duration-150">
                Nueva tecnología
            </a>

            <form action="/reporte-background-excel" method="POST">
                @csrf <!-- Token de protección contra CSRF -->
                <input type="hidden" name="estado" value="ACTIVO"> <!-- Parámetro enviado -->
                <button type="submit"
                    class="inline-flex items-center mx-1 px-4 mb-2 py-2 bg-green-600 border rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 transition ease-in-out duration-150">
                    <i class="fa-regular fa-file-excel" title="Reporte Excel Background Todos"></i>Background Todos
                </button>
            </form>

            <button id="reporte-pdf-screen"
                class="inline-flex items-center mx-1 px-4 mb-2 py-2 bg-red-700 border rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-900 transition ease-in-out duration-150"
                type="button" title="Reporte PDF - Screen">
                <i class="fa-regular fa-file-pdf"></i>Screen
            </button>

            <button data-dialog-target="modal" id="reporte-pdf-background"
                class="inline-flex items-center mx-1 px-4 mb-2 py-2 bg-red-700 border rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-900 transition ease-in-out duration-150"
                type="button" title="Reporte PDF - Background">
                <i class="fa-regular fa-file-pdf"></i>Background
            </button>

            <button id="importTecnologias"
                    class="inline-flex items-center mx-1 px-4 mb-2 py-2 bg-green-700 border rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 transition ease-in-out duration-150"
                    type="button"
                    title="Subir Archivo plano CSV">
                <i class="fa-solid fa-upload w-4 h-4 flex justify-center items-center" aria-hidden="true"></i>
            </button>
        </div>

        <div class="mt-2 md:flex justify-center">
            <div class="mx-2">
                <label for="dateDesde" class="block mb-2 text-sm text-slate-700">Seleccione fecha desde</label>
                <input type="text" id="dateDesde"
                    class="text-center w-full bg-transparent placeholder:text-slate-400 text-slate-700 text-sm border border-slate-200 rounded-md px-3 py-2 transition duration-300 ease focus:outline-none focus:border-slate-400 hover:border-slate-300 shadow-sm focus:shadow"
                    autocomplete="off">
            </div>
            <div class="mx-2">
                <label for="dateHasta" class="block mb-2 text-sm text-slate-700">Seleccione fecha hasta</label>
                <input type="text"
                    id="dateHasta"
                    class="text-center w-full bg-transparent placeholder:text-slate-400 text-slate-700 text-sm border border-slate-200 rounded-md px-3 py-2 transition duration-300 ease focus:outline-none focus:border-slate-400 hover:border-slate-300 shadow-sm focus:shadow"
                    autocomplete="off"></p>
            </div class="mx-2">
            <div class="mx-2 flex items-end justify-end mt-2 md:mt-0">
                <button id="btnBuscar" class="bg-blue-500 text-white px-4 py-2 rounded h-fit">Buscar</button>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table id="tecnologias-table" class="display" style="width:100%">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>NOMBRE</th>
                        <th>DESCRIPCION</th>
                        <th>ESTADO</th>
                        <th>CREADO</th>
                        <th></th>
                        <th></th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>

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

    <!-- Modal para importar tecnologías -->
    <div id="modal-import" class="pointer-events-none fixed inset-0 z-[999] grid h-screen w-screen place-items-center bg-black bg-opacity-60 opacity-0 backdrop-blur-sm transition-opacity duration-300">
        <div class="relative m-4 p-4 w-2/5 min-w-[40%] max-w-[40%] rounded-lg bg-white shadow-sm">
            <div class="flex shrink-0 justify-center items-center pb-4 text-xl font-medium text-slate-800">
                Importar tecnologías
            </div>
            <div class="relative border-t border-slate-200 py-4 leading-normal text-slate-600 font-light">
                <form id="importTecnologiasForm" method="post" enctype="multipart/form-data">
                    <div class="flex flex-wrap -mx-3 mb-6">
                        <div class="w-full px-3 mb-6">
                            <label for="fileImportarTecnologias" class="block mb-2 text-sm text-slate-700">
                                Archivo CSV
                            </label>
                            <input
                                id="fileImportarTecnologias"
                                name="fileImportarTecnologias"
                                type="file"
                                accept=".csv"
                                class="appearance-none block w-full bg-white border border-gray-300 rounded-md py-3 px-4 shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                            />
                        </div>
                    </div>
                </form>
            </div>
            <div class="flex shrink-0 flex-wrap items-center pt-4 justify-end">
                <button id="modal-close" class="rounded-md border border-transparent py-2 px-4 text-center text-sm transition-all text-slate-600 hover:bg-slate-100 focus:bg-slate-100 active:bg-slate-100 disabled:pointer-events-none disabled:opacity-50 disabled:shadow-none" type="button">
                    Cancel
                </button>
                <button id="import-procesar" class="rounded-md bg-green-600 py-2 px-4 border border-transparent text-center text-sm text-white transition-all shadow-md hover:shadow-lg focus:bg-green-700 focus:shadow-none active:bg-green-700 hover:bg-green-700 active:shadow-none disabled:pointer-events-none disabled:opacity-50 disabled:shadow-none ml-2" type="button">
                    Subir
                </button>
            </div>
        </div>
    </div>

    @push('scripts')
    <script src="{{ asset('js/dataTables/jquery-3.7.1.js') }}"></script>
    <script src="{{ asset('js/dataTables/datatables.js') }}"></script>
    <script src="https://code.jquery.com/ui/1.14.1/jquery-ui.js"></script>

    <script type="module">
        import { languageData, datePickerlanguageEs, parametrosPaginacion, searchValue, orderColumn, orderDescAsc, skip, take } from '/js/dataTables/commons.js';
        let isSubscribed = false;

        const columns = [
            { name: 'id' },
            { name: 'nombre' },
            { name: 'descripcion' },
            { name: 'estado' },
            { name: 'created_at' },
            { name: 'descargar', orderable: false, searchable: false},
            { name: 'action', orderable: false, searchable: false},
        ];

        document.addEventListener("DOMContentLoaded", function() {
            document.getElementById('reporte-pdf-screen').addEventListener('click', reportePdfScreen);
            document.getElementById('reporte-pdf-background').addEventListener('click', generarReporteBackground);
            document.getElementById('btnBuscar').addEventListener('click', searchRangeDate);

            /**
             * Inicializando datepicker y configuracion
             */
            $.datepicker.regional['es'] = datePickerlanguageEs;
            $.datepicker.setDefaults($.datepicker.regional['es']);

            const configDatePicker = {
                changeYear: true,
                changeMonth: true,
                dateFormat: "yy-mm-dd",
                maxDate: 0,
            };

            $("#dateDesde").datepicker(configDatePicker);
            $("#dateHasta").datepicker(configDatePicker);

            let table = new DataTable('#tecnologias-table', {
                search: {
                    return: true
                },
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('datatables.index') }}",
                    data: function (d) {
                        if ($('#dateDesde').val() != "" && $('#dateHasta').val() != "") {
                            d.dateDesde = $('#dateDesde').val(); // Capturar fecha desde
                            d.dateHasta = $('#dateHasta').val(); // Capturar fecha hasta
                        }
                    },
                    dataSrc: function (json) {
                        document.getElementById('loading').style.display = 'none'; // Ocultar el loading cuando los datos se cargan
                        return json.data;
                    },
                    beforeSend: function () {
                        document.getElementById('loading').style.display = 'flex';  // Mostrar el loading antes de que se realice la solicitud
                    },
                },
                columns: columns,
                language: languageData,
                order: [[0, 'desc']],
            });
        });

        function searchRangeDate(){
            let messageErros = "";
            if ($('#dateDesde').val() == "" && $('#dateHasta').val() != "") {
                messageErros += " - La fecha desde no puede ser vacía."
            }

            if ($('#dateHasta').val() == "" && $('#dateDesde').val() != "") {
                messageErros += " - La fecha hasta no puede ser vacía."
            }

            if (messageErros != ""){
                Swal.fire({ title: 'Verifique!', text: messageErros , icon: 'info' });
                return;
            }
            $('#tecnologias-table').DataTable().ajax.reload(); // Recargar la tabla con los nuevos filtros
        }

        function reportePdfScreen(){
            parametrosPaginacion('#tecnologias-table', columns);

            document.getElementById('loading').style.display = 'flex';
            fetch(`/reporte-pdf-screen?skip=${skip}&take=${take}&search=${searchValue}&orderColumn=${orderColumn}&order=${orderDescAsc}${getQueryUrlRangoFechas()}`)
                .then(response => response.blob())
                .then(blob  => {
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

        /**
         * Metodo que genera reporte en pdf en background tomando los datos de paginación del datatable
         */
        function generarReporteBackground() {
            parametrosPaginacion('#tecnologias-table', columns);
            document.getElementById('loading').style.display = 'flex';
            fetch(`/reporte-pdf-background?skip=${skip}&take=${take}&search=${searchValue}&orderColumn=${orderColumn}&order=${orderDescAsc}${getQueryUrlRangoFechas()}`)
                .then(response => response.json())
                .then(dataGet => {
                    document.getElementById('loading').style.display = 'none';

                    let ProgressBarSize = document.getElementById('ProgressBarSize');
                    ProgressBarSize.style.width = '5%';
                    ProgressBarSize.innerHTML = '1 %'

                    document.getElementById('modal').classList.remove('pointer-events-none', 'opacity-0');

                    if (!isSubscribed) {
                        window.Echo.channel('channel-name').listen('JobProgressUpdated', function(data) {
                            ProgressBarSize.style.width = data.progress + '%';
                            ProgressBarSize.innerHTML = data.progress + '%'

                            if (data.path) {
                                window.open(data.path, '_blank');
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
        }

        /**
         * Crear query string para consultar por rango de fechas.
         */
        function getQueryUrlRangoFechas(){
            let filterRangoFechas = "";
            if ($('#dateDesde').val() != "" && $('#dateHasta').val() != "") {
                filterRangoFechas += `&dateDesde=${$('#dateDesde').val()}&dateHasta=${$('#dateHasta').val()}`;
            }
            return filterRangoFechas;
        }
    </script>

    <script type="module">
        document.addEventListener('DOMContentLoaded', function() {
            document.getElementById('importTecnologias').addEventListener('click', openModalImport)
            document.getElementById('modal-close').addEventListener('click', closeModalImport)
            document.getElementById('import-procesar').addEventListener('click', importTecnologias)
        });

        function openModalImport(){
            $('#modal-import').removeClass('pointer-events-none').addClass('opacity-100 pointer-events-auto');
        }

        function closeModalImport(){
            //liampiando input file
            document.getElementById('fileImportarTecnologias').value = '';
            $('#modal-import').addClass('pointer-events-none').removeClass('opacity-100 pointer-events-auto');
        }

        async function importTecnologias(){
            document.getElementById('loading').style.display = 'flex';

            const formData = new FormData();
            formData.append('file', document.getElementById('fileImportarTecnologias').files[0]);
            formData.append('tipo','CSV');

            try {
                const respuesta = await fetch('/tecnologias-import',
                    {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        },
                        body: formData,
                    }
                );

                if (!respuesta.ok) {
                    throw new Error('Error al importar las tecnologías');
                }
                const datos = await respuesta.json();

                console.log(datos);
                document.getElementById('loading').style.display = 'none';
                Swal.fire({ title: 'Exitoso' ,text: datos.message, icon: "success" });

                //liampiando input file
                document.getElementById('fileImportarTecnologias').value = '';

                closeModalImport();
            } catch (error) {
                document.getElementById('loading').style.display = 'none';
                console.error('Hubo un error:', error);
                Swal.fire({ title: 'Error', text: datos.message , icon: 'error' });
                closeModalImport();
            }
        }
    </script>

    <script type="module">

        document.getElementById('tecnologias-table').addEventListener('click', async function(event) {
            if (event.target.classList.contains('view-pdf-technology-uploaded')) {
                viewPdfMinIo(event);
            }
        });

        async function viewPdfMinIo(event){
            try {
                document.getElementById('loading').style.display = 'flex';
                const pathFile = event.target.getAttribute('data-path');
                const respuesta = await fetch(`/view-pdf-minIo?pathFile=${pathFile}`);

                if (!respuesta.ok) {
                    throw new Error('Error al generar vista del pdf');
                }
                const datos = await respuesta.json();

                console.log(datos);
                document.getElementById('loading').style.display = 'none';

                //redireccionando a una nueva ventana
                window.open(datos.url, '_blank');
            } catch (error) {
                document.getElementById('loading').style.display = 'none';
                console.error('Hubo un error:', error);
                Swal.fire({ title: 'Error', text: datos.message , icon: 'error' });
            }
        }
    </script>
    @endpush
</x-app-layout>
