<?php

namespace App\Http\Controllers;

use App\Models\Log;
use Illuminate\Http\Request;

class LogController extends Controller
{
    /**
     * Display a listing of the logs.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (!in_array(auth()->user()->role, ['superadmin'])) {
            return response()->json(['message' => 'Acceso no autorizado'], 403);
        }
        // Obtiene todos los logs registrados
        $logs = Log::all();

        return response()->json($logs); // Retorna la lista de logs en formato JSON
    }

    /**
     * Store a newly created log in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // Valida la entrada
        $validated = $request->validate([
            'accionRealizada' => 'required|string|max:255',
            'tablaAfectada' => 'required|string|max:255',
            'idRegistroAfectado' => 'required|string|max:255',
            'idUsuario' => 'required|string|max:255',
        ]);

        // Crea un nuevo registro de log
        $log = Log::create([
            'accionRealizada' => $validated['accionRealizada'],
            'tablaAfectada' => $validated['tablaAfectada'],
            'idRegistroAfectado' => $validated['idRegistroAfectado'],
            'idUsuario' => $validated['idUsuario'],
        ]);

        return response()->json($log, 201); // Retorna el log recién creado
    }

    /**
     * Display the specified log.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        // Busca el log por su ID
        if (!in_array(auth()->user()->role, ['superadmin'])) {
            return response()->json(['message' => 'Acceso no autorizado'], 403);
        }
        $log = Log::find($id);

        // Si no se encuentra el log, retorna un mensaje de error
        if (!$log) {
            return response()->json(['message' => 'Log not found'], 404);
        }

        return response()->json($log); // Retorna el log encontrado
    }

    /**
     * Remove the specified log from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        // Verifica un rol que no existe, entonces no podra acceder nadie a eliminar logs.
        if (!in_array(auth()->user()->role, ['imposible'])) {
            return response()->json(['message' => 'Acceso no autorizado'], 403);
        }
        // Busca el log por su ID
        $log = Log::find($id);

        // Si no se encuentra el log, retorna un mensaje de error
        if (!$log) {
            return response()->json(['message' => 'Log not found'], 404);
        }

        // Elimina el log de la base de datos
        $log->delete();

        return response()->json(['message' => 'Log deleted successfully']);
    }
}
