<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use App\Models\Log;



class UserController extends Controller
{
    /**
     * Display a listing of the users.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // Obtiene todos los usuarios registrados
        $users = User::all();

        return response()->json($users); // Retorna la lista de usuarios en formato JSON
    }

    /**
     * Store a newly created user in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if (!in_array(auth()->user()->role, ['superadmin'])) {
            return response()->json(['message' => 'Acceso no autorizado'], 403);
        }
        // Valida la entrada
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8',
            'rol' => 'required|in:admin,user,superadmin',
        ]);

        // Crea un nuevo usuario
        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => $validated['password'],  // Deja la contraseña sin encriptar aquí
            'rol' => $validated['rol'],
        ]);

        $log = Log::create([
            'accionRealizada' => 'Usuario creado',
            'tablaAfectada' => 'users',
            'idRegistroAfectado' => $user->id,
            'idUsuario' => auth()->user()->id,
        ]);

        return response()->json($user, 201); // Retorna el usuario recién creado
    }

    /**
     * Display the specified user.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        // Busca el usuario por su ID
        $user = User::find($id);

        // Si no se encuentra el usuario, retorna un mensaje de error
        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        return response()->json($user); // Retorna el usuario encontrado
    }

    /**
     * Update the specified user in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        // Valida la entrada
        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'email' => 'sometimes|email|unique:users,email,' . $id,
            'password' => 'sometimes|string|min:8',
            'rol' => 'sometimes|in:admin,user,superadmin',
        ]);

        // Busca el usuario por su ID
        $user = User::find($id);

        // Si no se encuentra el usuario, retorna un mensaje de error
        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        // Actualiza el usuario con los datos validados
        $user->update(array_filter($validated)); // `array_filter` elimina valores nulos

        // Si la contraseña fue modificada, encripta la nueva
        if (isset($validated['password'])) {
            $user->password = Hash::make($validated['password']);
            $user->save();
        }

        return response()->json($user); // Retorna el usuario actualizado
    }

    /**
     * Remove the specified user from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        // Busca el usuario por su ID
        $user = User::find($id);

        // Si no se encuentra el usuario, retorna un mensaje de error
        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        // Elimina el usuario de la base de datos
        $user->delete();

        return response()->json(['message' => 'User deleted successfully']);
    }

    public function login(Request $request)
    {
        // Validar los datos de entrada
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);
    
        // Buscar al usuario por email
        $user = User::where('email', $credentials['email'])->first();
    
        if (!$user) {
            \Log::info('El correo no está registrado.');
            return response()->json(['message' => 'El correo electrónico no está registrado.'], 404);
        }
    
        \Log::info('Password hash from database: ' . $user->password); // Log el hash de la base de datos
        \Log::info('Password from request: ' . $credentials['password']); // Log la contraseña de la solicitud
    
        // Validar la contraseña
        if (!Hash::check($credentials['password'], $user->password)) {
            \Log::info('Las contraseñas no coinciden.');
            return response()->json(['message' => 'La contraseña es incorrecta.'], 401);
        }
    
        // Generar el token de acceso
        $token = $user->createToken('API Token')->plainTextToken;
    
        // Retornar el token en la respuesta
        return response()->json([
            'message' => 'Inicio de sesión exitoso.',
            'token' => $token
        ], 200);
    }
    public function getAuthenticatedUser()
    {
        // Obtener el usuario autenticado
        $user = Auth::user();

        // Si no hay un usuario autenticado, retornar un error
        if (!$user) {
            return response()->json(['message' => 'No estás autenticado.'], 401);
        }

        // Retornar los datos del usuario autenticado
        return response()->json($user);
    }
    
    
    
}



