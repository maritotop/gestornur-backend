<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\Log;
use Illuminate\Http\Request;

class StudentController extends Controller
{
    /**
     * Display a listing of the students.
     */
    public function index()
    {
        // Todos los usuarios autenticados pueden acceder
        $students = Student::all();
        return response()->json($students);
    }

    /**
     * Store a newly created student in storage.
     */
    public function store(Request $request)
    {
        \Log::info('Usuario autenticado:', ['role' => auth()->user()->role]);
        // Solo admin y superadmin pueden acceder

        
        $validated = $request->validate([
            'nroRegistro' => 'required|unique:students,nroRegistro',
            'nombreCompleto' => 'required|string|max:255',
        ]);

        $student = Student::create($validated);

        Log::create([
            'accionRealizada' => 'Estudiante creado',
            'tablaAfectada' => 'students',
            'idRegistroAfectado' => $student->id,
            'idUsuario' => auth()->user()->id,
        ]);

        return response()->json($student, 201);
    }

    /**
     * Display the specified student.
     */
    public function show($id)
    {
        // Todos los usuarios autenticados pueden acceder
        $student = Student::find($id);

        if (!$student) {
            return response()->json(['message' => 'Estudiante no encontrado'], 404);
        }

        return response()->json($student);
    }

    /**
     * Update the specified student in storage.
     */
    public function update(Request $request, $id)
    {
        // Solo admin y superadmin pueden acceder
        if (!in_array(auth()->user()->role, ['admin', 'superadmin'])) {
            return response()->json(['message' => 'Acceso no autorizado'], 403);
        }

        $student = Student::find($id);

        if (!$student) {
            return response()->json(['message' => 'Estudiante no encontrado'], 404);
        }

        $validated = $request->validate([
            'nroRegistro' => 'nullable|unique:students,nroRegistro,' . $id,
            'nombreCompleto' => 'nullable|string|max:255',
        ]);

        $student->update($validated);

        Log::create([
            'accionRealizada' => 'Estudiante actualizado',
            'tablaAfectada' => 'students',
            'idRegistroAfectado' => $student->id,
            'idUsuario' => auth()->user()->id,
        ]);

        return response()->json($student);
    }

    /**
     * Remove the specified student from storage.
     */
    public function destroy($id)
    {
        // Solo superadmin puede acceder
        if (!in_array(auth()->user()->role, ['admin', 'superadmin'])) {
            return response()->json(['message' => 'Acceso no autorizado'], 403);
        }

        $student = Student::find($id);

        if (!$student) {
            return response()->json(['message' => 'Estudiante no encontrado'], 404);
        }

        $student->delete();

        Log::create([
            'accionRealizada' => 'Estudiante eliminado',
            'tablaAfectada' => 'students',
            'idRegistroAfectado' => $id,
            'idUsuario' => auth()->user()->id,
        ]);

        return response()->json(['message' => 'Estudiante eliminado correctamente']);
    }
}
