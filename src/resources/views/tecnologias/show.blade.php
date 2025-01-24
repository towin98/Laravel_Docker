<x-app-layout>
    <div class="container mx-auto sm:w-3/6 w-11/12">

        <h3
            class="text-center my-3 font-semibold leading-snug tracking-normal text-slate-800 mx-auto w-full text-lg max-w-md lg:max-w-xl lg:text-2xl">
            {{ isset($tecnologia) ? 'Editar' : 'Crear' }} Tecnología
        </h3>

        <!-- Formulario -->
        <form
            action="{{ isset($tecnologia) ? route('tecnologias.update', $tecnologia->id) : route('tecnologias.store') }}"
            method="POST">
            @csrf
            @if (isset($tecnologia))
                @method('PUT')
            @endif

            <div class="mb-3">
                <label for="nombre" class="">Nombre</label>
                <input type="text"
                    class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm mt-1 block w-full"
                    id="nombre" name="nombre" value="{{ old('nombre', $tecnologia->nombre ?? '') }}" required>
                @error('nombre')
                    <div class="text-red-500">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="descripcion" class="">Descripción</label>
                <textarea class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm mt-1 block w-full"
                    id="descripcion" name="descripcion" required>{{ old('descripcion', $tecnologia->descripcion ?? '') }}</textarea>
                @error('descripcion')
                    <div class="text-red-500">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="estado" class="block text-sm font-medium text-gray-700">Estado</label>
                <select
                    class="mt-1 block w-full py-2 px-3 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:ring-offset-2 focus:ring-2"
                    id="estado" name="estado" required>
                    <option value="">[SELECCIONE]</option>
                    <option value="ACTIVO" {{ old('estado', $tecnologia->estado ?? '') == 'ACTIVO' ? 'selected' : '' }}>
                        ACTIVO</option>
                    <option value="INACTIVO" {{ old('estado', $tecnologia->estado ?? '') == 'INACTIVO' ? 'selected' : '' }}>
                        INACTIVO</option>
                </select>
                @error('estado')
                    <div class="text-red-500">{{ $message }}</div>
                @enderror
            </div>

            <div class="flex">
                <div class="flex-initial">
                    <x-primary-button>{{ __(isset($tecnologia) ? 'Actualizar ' : 'Crear ') }}Tecnología</x-primary-button>
                </div>
                <div class="flex-initial">
                    <x-responsive-nav-link :href="route('tecnologias.index')">
                        {{ __('Regresar') }}
                    </x-responsive-nav-link>
                </div>
            </div>
        </form>

        {{-- Mensaje de éxito --}}
        @if (session('success'))
            <div class="text-green-800 mt-2">
                {{ session('success') }}
            </div>
        @endif

        {{-- @if ($errors->any())
            <div class="text-red-800 mt-2">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif --}}

        {{-- Mensaje de error --}}
        @if (session('error'))
            <div class="text-green-800 mt-2">
                {{ session('error') }}
            </div>
        @endif
    </div>
</x-app-layout>
