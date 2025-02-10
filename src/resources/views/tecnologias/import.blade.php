<x-app-layout>
    <div class="container mx-auto sm:w-3/6 w-11/12">

        <h3
            class="text-center my-3 font-semibold leading-snug tracking-normal text-slate-800 mx-auto w-full text-lg max-w-md lg:max-w-xl lg:text-2xl">
            Subir Tecnologías
        </h3>

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

            <div class="grid grid-cols-2 gap-4">
                <div class="mb-3 w-full max-w-sm min-w-[200px]">
                    <label for="up-pdf" class="block mb-2 text-sm text-slate-700">
                        Subir Archivo Plano
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
            </div>

            <div class="flex justify-end">
                <div class="flex items-end pr-2">
                    <a class="underline text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" href="{{ route('laravel-datatable') }}">
                        {{ __('Regresar') }}
                    </a>
                </div>
                <div class="flex-initial">
                    <x-primary-button>{{ __('Subir') }}</x-primary-button>
                </div>
            </div>
        </form>
    </div>
</x-app-layout>
