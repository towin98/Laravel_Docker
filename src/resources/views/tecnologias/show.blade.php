<x-app-layout>
    <div class="container mx-auto">
        <h4 class="text-center">{{ isset($tecnologia) ? 'Editar' : 'Crear' }} Tecnología</h4>

        <!-- Formulario -->
        <form action="{{ isset($tecnologia) ? route('tecnologias.update', $tecnologia->id) : route('tecnologias.store') }}" method="POST">
            @csrf
            @if(isset($tecnologia))
                @method('PUT')
            @endif

            <div class="mb-3">
                <label for="nombre" class="form-label">Nombre</label>
                <input type="text" class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm mt-1 block w-full" id="nombre" name="nombre" value="{{ old('nombre', $tecnologia->nombre ?? '') }}" required>
                @error('nombre')
                    <div class="text-danger">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="descripcion" class="form-label">Descripción</label>
                <textarea class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm mt-1 block w-full" id="descripcion" name="descripcion" required>{{ old('descripcion', $tecnologia->descripcion ?? '') }}</textarea>
                @error('descripcion')
                    <div class="text-danger">{{ $message }}</div>
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
        @if(session('success'))
        <div class="text-green-800 mt-2">
            {{ session('success') }}
        </div>
        @endif

        {{-- Mostrar mensaje de error --}}
        @if(isset($error))
        <div class="text-red-800 mt-2">
            {{ $error }}
        </div>
        @endif

        {{-- Mensaje de error --}}
        @if(session('error'))
        <div class="text-green-800 mt-2">
            {{ session('error') }}
        </div>
        @endif
    </div>
</x-app-layout>
