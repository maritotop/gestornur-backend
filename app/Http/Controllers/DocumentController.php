<?php

namespace App\Http\Controllers;

use App\Models\Document;
use App\Models\Student;
use App\Models\Log;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class DocumentController extends Controller
{
    /**
     * Display a listing of the documents.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try {
            $documents = Document::all(); // Obtiene todos los documentos
            return response()->json($documents); // Retorna la lista de documentos en formato JSON
        } catch (\Exception $e) {
            \Log::error("Error al obtener documentos: " . $e->getMessage());
            return response()->json(['message' => 'Error al obtener documentos', 'error' => $e->getMessage()], 500);
        }
    }
    

    /**
     * Show the form for creating a new document.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        // Puede retornar una vista con el formulario de creación si usas vistas,
        // pero por ahora retornamos un mensaje básico indicando que se puede crear un documento.
        return response()->json(['message' => 'Create a new document']);
    }

    /**
     * Store a newly created document in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try {
            // Código de almacenamiento
            $validated = $request->validate([
                'estudianteId' => 'required|exists:students,id',
                'tipo' => 'required|in:certificadoNacimiento,tituloBachiller,carnetIdentidad,certificadoEstudio,otroDocumento',
                'file' => 'required|file|mimes:pdf,jpg,png|max:2048',
            ]);
    
            // Almacenar el archivo
            $documentoURL = $request->file('file')->store('uploads', 'public');
    
            // Crear el documento en la base de datos
            $document = Document::create([
                'estudianteId' => $validated['estudianteId'],
                'tipo' => $validated['tipo'],
                'documentoURL' => $documentoURL,
                'estado' => 'activo',
            ]);

            // Insertar en la tabla log de auditoría
            Log::create([
                'accionRealizada' => 'Documento creado',
                'tablaAfectada' => 'documents',
                'idRegistroAfectado' => $document->id,
                'idUsuario' => auth()->user()->id,
            ]);

    
            return response()->json(['message' => 'Documento creado exitosamente', 'document' => $document], 201);
        } catch (\Exception $e) {
            // Captura el error y lo imprime en el log
            \Log::error("Error al guardar el documento: " . $e->getMessage());
            return response()->json(['message' => 'Error al guardar el documento', 'error' => $e->getMessage()], 500);
        }
    }
    


    /**
     * Display the specified document.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $document = Document::find($id);

        if (!$document) {
            return response()->json(['message' => 'Document not found'], 404);
        }

        return response()->json($document); // Devuelve el documento encontrado
    }

    /**
     * Show the form for editing the specified document.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        // Devuelve el formulario de edición, si usas vistas.
        return response()->json(['message' => 'Edit document ' . $id]);
    }

    /**
     * Update the specified document in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
{
    $document = Document::find($id);

    if (!$document) {
        return response()->json(['message' => 'Document not found'], 404);
    }

    if (!in_array(auth()->user()->role, ['admin', 'superadmin'])) {
        return response()->json(['message' => 'Acceso no autorizado'], 403);
    }
    try {
        $validated = $request->validate([
            'tipo' => 'nullable|in:certificadoNacimiento,tituloBachiller,carnetIdentidad,certificadoEstudio,otroDocumento',
            'documentoURL' => 'nullable|file|mimes:pdf,jpg,png',
            'estado' => 'nullable|in:activo,archivado,eliminado',
        ]);

        if ($request->hasFile('documentoURL')) {
            // Verificar si el archivo anterior existe antes de eliminarlo
            if (Storage::exists($document->documentoURL)) {
                Storage::delete($document->documentoURL);
            }
            // Almacenar el nuevo archivo
            $documentoURL = $request->file('documentoURL')->store('uploads', 'public');
            $document->documentoURL = $documentoURL;
        }

        if ($request->has('tipo')) {
            $document->tipo = $validated['tipo'];
        }

        if ($request->has('estado')) {
            $document->estado = $validated['estado'];
        }

        $document->save(); // Guarda los cambios

        Log::create([
            'accionRealizada' => 'Documento actualizado',
            'tablaAfectada' => 'documents',
            'idRegistroAfectado' => $document->id,
            'idUsuario' => auth()->user()->id,
        ]);

        return response()->json($document); // Retorna el documento actualizado
    } catch (\Exception $e) {
        \Log::error("Error al actualizar el documento: " . $e->getMessage());
        return response()->json(['message' => 'Error al actualizar el documento', 'error' => $e->getMessage()], 500);
    }
}


    /**
     * Remove the specified document from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $document = Document::find($id);

        if (!$document) {
            return response()->json(['message' => 'Document not found'], 404);
        }

        if (!in_array(auth()->user()->role, ['superadmin'])) {
            return response()->json(['message' => 'Acceso no autorizado'], 403);
        }
        // Eliminar el archivo relacionado
        //Storage::delete($document->documentoURL);

        // Eliminar el registro de la base de datos
        //$document->delete();

        // Cambiar el estado del documento a "eliminado"
        $document->estado = 'eliminado';
        $document->save();

        Log::create([
            'accionRealizada' => 'Documento eliminado',
            'tablaAfectada' => 'documents',
            'idRegistroAfectado' => $id,
            'idUsuario' => auth()->user()->id,
        ]);

        return response()->json(['message' => 'Document deleted successfully']);
    }
    public function getDocuments($id)
    {
        // Obtén los documentos relacionados al estudiante
        $documents = Document::where('estudianteId', $id)->get();    
        return response()->json($documents);
        
    }
    
}
