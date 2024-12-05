@extends('layouts.app') <!-- Extiende tu layout principal, si tienes uno -->

@section('content')
<div class="container">
    <h1>Lista de Documentos</h1>

    <!-- Botón para agregar un nuevo documento -->
    <a href="{{ route('documents.create') }}" class="btn btn-primary mb-3">Agregar Nuevo Documento</a>

    <!-- Tabla para mostrar los documentos -->
    <table class="table table-striped">
        <thead>
            <tr>
                <th>#</th>
                <th>Tipo</th>
                <th>Fecha de Creación</th>
                <th>Fecha de Modificación</th>
                <th>Estado</th>
                <th>Documento</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            @foreach($documents as $document)
            <tr>
                <td>{{ $document->id }}</td>
                <td>{{ ucfirst($document->tipo) }}</td>
                <td>{{ $document->fechaCreacion->format('d/m/Y H:i:s') }}</td>
                <td>{{ $document->fechaModificacion ? $document->fechaModificacion->format('d/m/Y H:i:s') : 'N/A' }}</td>
                <td>{{ ucfirst($document->estado) }}</td>
                <td>
                    <a href="{{ asset('storage/' . $document->documentoURL) }}" target="_blank" class="btn btn-info">Ver Documento</a>
                </td>
                <td>
                    <!-- Botón para editar el documento -->
                    <a href="{{ route('documents.edit', $document->id) }}" class="btn btn-warning">Editar</a>

                    <!-- Formulario para eliminar el documento -->
                    <form action="{{ route('documents.destroy', $document->id) }}" method="POST" style="display: inline-block;" onsubmit="return confirm('¿Estás seguro de que quieres eliminar este documento?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">Eliminar</button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <!-- Paginación (si tienes muchos documentos) -->
    {{ $documents->links() }}
</div>
@endsection
