<!-- resources/views/tecnologias/index.blade.php -->

@extends('layouts.app')

@section('title', 'Tecnologías')

@section('content')
    <div class="container">
        <h4 class="text-center mt-3">Tecnologías</h4>

        @if(session('success'))
        <div class="alert alert-success mt-2">
            {{ session('success') }}
        </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger mt-2">
                {{ session('error') }}
            </div>
        @endif

        <a href="{{ route('tecnologia.create') }}" class="btn btn-primary my-3">Nueva tecnología</a>
        <ul>
            @foreach($tecnologias as $technology)
                <li>
                    {{ $technology->nombre }}: {{ $technology->descripcion }}
                    <a href="{{ route('tecnologias.show', $technology->id) }}" class="text-decoration-none">✏️</a>
                    <form action="{{ route('tecnologias.destroy', $technology->id) }}" method="POST" style="display: inline;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-link text-decoration-none p-0 mx-2" style="border: none; background: none;">
                            ❌
                        </button>
                    </form>
                </li>
            @endforeach
        </ul>
    </div>
@endsection
