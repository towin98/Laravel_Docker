<!-- Opciones de editar y eliminar -->
<div class="flex">
    <a href="{{ url('/tecnologias/' . $tecnologia->id) }}" class="text-green-700" title="Editar Registro">
        <i class="fa fa-pencil"></i>
    </a>
    <form action="{{ url('/tecnologias/' . $tecnologia->id) }}" method="POST" style="display: inline;">
        @csrf
        @method('DELETE')
        <button type="submit" class="text-red-700 p-0 mx-2" title="Eliminar Registro">
            <i class="fa-regular fa-trash-can"></i>
        </button>
    </form>
</div>
