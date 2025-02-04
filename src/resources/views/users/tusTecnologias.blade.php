<x-app-layout>
    <div class="container mx-auto lg:w-2/4">
        <h3
            class="text-center my-3 font-semibold leading-snug tracking-normal text-slate-800 mx-auto w-full text-lg max-w-md lg:max-w-xl lg:text-2xl">
            Tus tecnologías
        </h3>

        <div
            class="flex flex-col rounded-lg bg-white shadow-sm p-2 my-6 border border-slate-200">
            <table class="w-full text-left table-auto min-w-max">
                <thead>
                    <tr>
                        <th class="p-4 border-b border-gray-100 bg-gray-50">
                            <p
                                class="block font-sans text-sm antialiased font-normal leading-none text-blue-gray-900 opacity-70">
                                #
                            </p>
                        </th>
                        <th class="p-4 border-b border-gray-100 bg-gray-50">
                            <p
                                class="block font-sans text-sm antialiased font-normal leading-none text-blue-gray-900 opacity-70">
                                Nombre
                            </p>
                        </th>
                        <th class="p-4 border-b border-gray-100 bg-gray-50">
                            <p
                                class="block font-sans text-sm antialiased font-normal leading-none text-blue-gray-900 opacity-70">
                                Estado
                            </p>
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($tusTecnologias as $tecnologia)
                    <tr>
                        <td class="p-4 border-b border-gray-200">
                            <p
                                class="block font-sans text-sm antialiased font-normal leading-normal text-blue-gray-900">
                                {{ $tecnologia->id }}
                            </p>
                        </td>
                        <td class="p-4 border-b border-gray-200">
                            <p
                                class="block font-sans text-sm antialiased font-normal leading-normal text-blue-gray-900">
                                {{ $tecnologia->nombre }}
                            </p>
                        </td>
                        <td class="p-4 border-b border-gray-200">
                            <p
                                class="block font-sans text-sm antialiased font-normal leading-normal {{ $tecnologia->estado == 'INACTIVO' ? 'text-red-700' : 'text-green-700' }}">
                                {{ $tecnologia->estado }}
                            </p>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>





        </ul>
    </div>
</x-app-layout>
