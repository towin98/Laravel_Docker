<x-app-layout>
    <div class="container mx-auto sm:w-3/6 w-11/12">

        <h3
            class="text-center my-3 font-semibold leading-snug tracking-normal text-slate-800 mx-auto w-full text-lg max-w-md lg:max-w-xl lg:text-2xl">
            {{ isset($tecnologia) ? 'Editar' : 'Nueva' }} Tecnología
        </h3>

        {{-- Mensaje de éxito --}}
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

        <!-- Formulario -->
        <form
            action="{{ isset($tecnologia) ? route('tecnologias.update', $tecnologia->id) : route('tecnologias.store') }}"
            method="POST"
            enctype="multipart/form-data">
            @csrf
            @if (isset($tecnologia))
                @method('PUT')
            @endif

            <div class="mb-3">
                <label for="nombre" class="block mb-2 text-sm text-slate-700">Nombre</label>
                <input type="text"
                    class="w-full bg-transparent placeholder:text-slate-400 text-slate-700 text-sm border border-slate-200 rounded-md px-3 py-2 transition duration-300 ease focus:outline-none focus:border-slate-400 hover:border-slate-300 shadow-sm focus:shadow"
                    id="nombre" name="nombre" value="{{ old('nombre', $tecnologia->nombre ?? '') }}" required>
                @error('nombre')
                    <div class="text-red-500">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="descripcion" class="block mb-2 text-sm text-slate-700">
                    Descripción
                </label>
                <textarea class="w-full bg-transparent placeholder:text-slate-400 text-slate-700 text-sm border border-slate-200 rounded-md px-3 py-2 transition duration-300 ease focus:outline-none focus:border-slate-400 hover:border-slate-300 shadow-sm focus:shadow"
                    id="descripcion" name="descripcion" required>{{ old('descripcion', $tecnologia->descripcion ?? '') }}</textarea>
                @error('descripcion')
                    <div class="text-red-500">{{ $message }}</div>
                @enderror
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div class="mb-3 w-full max-w-sm min-w-[200px]">
                    <label for="up-pdf" class="block mb-2 text-sm text-slate-700">
                        Subir Pdf
                    </label>
                    <input
                        type="file"
                        id="up-pdf"
                        name="pdf"
                        class="w-full bg-transparent placeholder:text-slate-400 text-slate-700 text-sm border border-slate-200 rounded-md px-3 transition duration-300 ease focus:outline-none focus:border-slate-400 hover:border-slate-300 shadow-sm focus:shadow" placeholder="Type here..."
                        style="padding: 6px 10px 6px 10px;">
                    @error('pdf')
                        <div class="text-red-500">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="estado" class="block mb-2 text-sm text-slate-700">
                        Estado
                    </label>
                    <div class="relative">
                        <select
                            class="w-full bg-transparent placeholder:text-slate-400 text-slate-700 text-sm border border-slate-200 rounded py-2 transition duration-300 ease focus:outline-none focus:border-slate-400 hover:border-slate-400 shadow-sm focus:shadow-md appearance-none cursor-pointer"
                            id="estado"
                            name="estado"
                            required>
                            <option value="">[SELECCIONE]</option>
                            <option value="ACTIVO" {{ old('estado', $tecnologia->estado ?? '') == 'ACTIVO' ? 'selected' : '' }}>ACTIVO</option>
                            <option value="INACTIVO" {{ old('estado', $tecnologia->estado ?? '') == 'INACTIVO' ? 'selected' : '' }}>INACTIVO</option>
                        </select>
                    </div>
                    @error('estado')
                        <div class="text-red-500">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="flex justify-end">
                <div class="flex items-end pr-2">
                    <a class="underline text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" href="{{ route('laravel-datatable') }}">
                        {{ __('Regresar') }}
                    </a>
                </div>
                <div class="flex-initial">
                    <x-primary-button>{{ __(isset($tecnologia) ? 'Actualizar ' : 'Guardar ') }}</x-primary-button>
                </div>
            </div>
        </form>
    </div>
</x-app-layout>
