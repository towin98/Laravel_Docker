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
                        <th></th>
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
                <button onclick="asignar()" class="rounded-md bg-green-600 py-2 px-4 border border-transparent text-center text-sm text-white transition-all shadow-md hover:bg-green-700 ml-2">Asignar</button>
            </div>
        </div>
    </div>

    @push('scripts')
    <script src="{{ asset('js/dataTables/jquery-3.7.1.js') }}"></script>
    <script src="{{ asset('js/dataTables/datatables.js') }}"></script>
    <script src="{{ asset('js/dataTables/dataTables.select.js') }}"></script>
    <script src="{{ asset('js/dataTables/select.dataTables.js') }}"></script>
    <script src="{{ asset('js/usersList.js') }}"></script>
    @endpush
</x-app-layout>
