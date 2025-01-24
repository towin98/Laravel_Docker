<!-- resources/views/tecnologias/index.blade.php -->

<x-app-layout>
    <div class="container mx-auto mb-10">
        <h3
            class="text-center my-3 font-semibold leading-snug tracking-normal text-slate-800 mx-auto w-full text-lg max-w-md lg:max-w-xl lg:text-2xl">
            Tecnologías
        </h3>

        @if (session('success'))
            <x-alert id="alert-1" color="gray" class="mt-2">
                {{ session('success') }}
            </x-alert>
        @endif

        @if (session('error'))
            <x-alert id="alert-1" color="red" class="mt-2">
                {{ session('error') }}
            </x-alert>
        @endif
        <div class="flex">
            <a href="{{ route('tecnologia.create') }}"
                class="inline-flex items-center px-4 mb-2 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                Nueva tecnología
            </a>

            <form action="/reporte-background-excel" method="POST">
                @csrf <!-- Token de protección contra CSRF -->
                <input type="hidden" name="estado" value="ACTIVO"> <!-- Parámetro enviado -->
                <button type="submit"
                    class="inline-flex items-center mx-2 px-4 mb-2 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                    <i class="fa-regular fa-file-excel"></i>Reporte Excel Background Todos
                </button>
            </form>

            <a href="{{ route('tecnologias.reportPdf', ['skip' => (($currentPage * $take) - $take), 'take' => 10]) }}"
                class="inline-flex items-center px-4 mb-2 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                <i class="fa-regular fa-file-pdf"></i>Reporte PDF
            </a>
        </div>
        <div
            class="relative flex flex-col w-full h-full overflow-scroll text-gray-700 bg-white shadow-md  bg-clip-border">
            <table class="w-full text-left table-auto min-w-max">
                <thead>
                    <tr>
                        <th class="p-4 border-b border-blue-gray-100 bg-blue-gray-50">
                            <p
                                class="block font-sans text-sm antialiased font-normal leading-none text-blue-gray-900 opacity-70">
                                #
                            </p>
                        </th>
                        <th class="p-4 border-b border-blue-gray-100 bg-blue-gray-50">
                            <p
                                class="block font-sans text-sm antialiased font-normal leading-none text-blue-gray-900 opacity-70">
                                NOMBRE
                            </p>
                        </th>
                        <th class="p-4 border-b border-blue-gray-100 bg-blue-gray-50">
                            <p
                                class="block font-sans text-sm antialiased font-normal leading-none text-blue-gray-900 opacity-70">
                                DESCRIPCION
                            </p>
                        </th>
                        <th class="p-4 border-b border-blue-gray-100 bg-blue-gray-50">
                            <p
                                class="block font-sans text-sm antialiased font-normal leading-none text-blue-gray-900 opacity-70">
                                ESTADO
                            </p>
                        </th>
                        <th class="p-4 border-b border-blue-gray-100 bg-blue-gray-50">
                            <p
                                class="block font-sans text-sm antialiased font-normal leading-none text-blue-gray-900 opacity-70">
                            </p>
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($tecnologias as $technology)
                        <tr>
                            <td class="p-4 border-b border-blue-gray-50">
                                <p
                                    class="block font-sans text-sm antialiased font-normal leading-normal text-blue-gray-900 uppercase">
                                    {{ $technology->id }}
                                </p>
                            </td>
                            <td class="p-4 border-b border-blue-gray-50">
                                <p
                                    class="block font-sans text-sm antialiased font-normal leading-normal text-blue-gray-900 uppercase">
                                    {{ $technology->nombre }}
                                </p>
                            </td>
                            <td class="p-4 border-b border-blue-gray-50">
                                <p
                                    class="block font-sans text-sm antialiased font-normal leading-normal text-blue-gray-900 w-96 uppercase">
                                    {{ $technology->descripcion }}
                                </p>
                            </td>
                            <td class="p-4 border-b border-blue-gray-50">
                                <p
                                    class="block font-sans text-sm antialiased font-normal leading-normal text-blue-gray-900 uppercase">
                                    {{ $technology->estado }}
                                </p>
                            </td>

                            <td class="p-4 border-b border-blue-gray-50">
                                <a href="{{ route('tecnologias.show', $technology->id) }}"
                                    class="text-green-700">Editar</a>
                                <form action="{{ route('tecnologias.destroy', $technology->id) }}" method="POST"
                                    style="display: inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-700 p-0 mx-2"
                                        style="border: none; background: none;">
                                        Eliminar
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <div class="flex items-center justify-center gap-8 mt-4">
                <!-- Botón de página anterior -->
                @if ($currentPage > 1)
                    <a href="{{ route('tecnologias.index', ['skip' => ($currentPage - 2) * $take, 'take' => $take]) }}"
                        class="rounded-md border border-slate-300 p-2.5 text-sm transition-all shadow-sm text-slate-600 hover:bg-slate-800 hover:text-white">
                        Anterior
                    </a>
                @else
                    <button disabled
                        class="rounded-md border border-slate-300 p-2.5 text-sm text-slate-600 opacity-50 pointer-events-none">
                        Anterior
                    </button>
                @endif

                <!-- Información de página actual -->
                <p class="text-slate-600">
                    Página <strong>{{ $currentPage }}</strong> de <strong>{{ $totalPages }}</strong>
                </p>

                <!-- Botón de página siguiente -->
                @if ($currentPage < $totalPages)
                    <a href="{{ route('tecnologias.index', ['skip' => $currentPage * $take, 'take' => $take]) }}"
                        class="rounded-md border border-slate-300 p-2.5 text-sm transition-all shadow-sm text-slate-600 hover:bg-slate-800 hover:text-white">
                        Siguiente
                    </a>
                @else
                    <button disabled
                        class="rounded-md border border-slate-300 p-2.5 text-sm text-slate-600 opacity-50 pointer-events-none">
                        Siguiente
                    </button>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
