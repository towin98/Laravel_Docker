@extends('layouts.app')

@section('title', isset($tecnologia) ? 'Editar Tecnología' : 'Crear Tecnología')

@section('content')
    <h4 class="text-center">{{ isset($tecnologia) ? 'Editar' : 'Crear' }} Tecnología</h4>

    <!-- Formulario -->
    <form action="{{ isset($tecnologia) ? route('tecnologias.update', $tecnologia->id) : route('tecnologias.store') }}" method="POST">
        @csrf
        @if(isset($tecnologia))
            @method('PUT')
        @endif

        <div class="mb-3">
            <label for="nombre" class="form-label">Nombre</label>
            <input type="text" class="form-control" id="nombre" name="nombre" value="{{ old('nombre', $tecnologia->nombre ?? '') }}" required>
            @error('nombre')
                <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="descripcion" class="form-label">Descripción</label>
            <textarea class="form-control" id="descripcion" name="descripcion" required>{{ old('descripcion', $tecnologia->descripcion ?? '') }}</textarea>
            @error('descripcion')
                <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>

        <button type="submit" class="btn btn-primary">{{ isset($tecnologia) ? 'Actualizar' : 'Crear' }} Tecnología</button>
        <a href="{{ route('tecnologias.index') }}" class="btn btn-secondary">Regresar</a>
    </form>

    {{-- Mensaje de éxito --}}
    @if(session('success'))
    <div class="alert alert-success mt-2">
        {{ session('success') }}
    </div>
    @endif

    {{-- Mostrar mensaje de error --}}
    @if(isset($error))
    <div class="alert alert-danger mt-2">
        {{ $error }}
    </div>
    @endif

    {{-- Mensaje de error --}}
    @if(session('error'))
        <div class="alert alert-danger mt-2">
            {{ session('error') }}
        </div>
    @endif
@endsection
