<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DocumentController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\LogController;
use App\Http\Controllers\UserController;


Route::get('/', function () {
    return view('welcome');
});

Route::resource('documents', DocumentController::class);
Route::resource('students', StudentController::class);
Route::resource('logs', LogController::class);
Route::resource('users', UserController::class);
