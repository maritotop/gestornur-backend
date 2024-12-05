<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DocumentController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\LogController;
use App\Http\Controllers\UserController;

Route::post('/login', [UserController::class, 'login']);

// Asegúrate de que estas rutas solo sean accesibles por usuarios autenticados
Route::middleware('auth:sanctum')->group(function () {
    Route::resource('documents', DocumentController::class);
    Route::resource('students', StudentController::class);
    Route::resource('logs', LogController::class);
    Route::resource('users', UserController::class);
    
    // Ruta para obtener el usuario autenticado
    Route::get('/user/logueado', [UserController::class, 'getAuthenticatedUser']);
    //Ruta para obtener la lista de documentos pertenecientes a un estudiante
    Route::get('/students/{id}/documents', [DocumentController::class, 'getDocuments']);
});

