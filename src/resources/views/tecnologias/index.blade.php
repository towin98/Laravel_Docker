<!-- resources/views/tecnologias/index.blade.php -->

<x-app-layout>
    <div class="container mx-auto">
        <h4 class="text-center my-3">Tecnologías</h4>

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

        <a href="{{ route('tecnologia.create') }}"
            class="inline-flex items-center px-4 mb-2 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
            Nueva tecnología
        </a>

        <div
            class="relative flex flex-col w-full h-full overflow-scroll text-gray-700 bg-white shadow-md  bg-clip-border">
            <table class="w-full text-left table-auto min-w-max">
                <thead>
                    <tr>
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
                                    ACTIVO
                                </p>
                            </td>

                            <td class="p-4 border-b border-blue-gray-50">
                                <a href="{{ route('tecnologias.show', $technology->id) }}" class="text-green-700">Editar</a>
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
        </div>
    </div>
</x-app-layout>
