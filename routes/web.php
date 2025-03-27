<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;

Route::get('/', function () {
    return view('welcome');
});

// Rutas de UserController
Route::get('/prueba', [UserController::class, 'prueba']); 
Route::post('/api/register', [UserController::class, 'register']);
Route::post('/api/login', [UserController::class, 'login']);
Route::post('/api/user/update', [UserController::class, 'update']);
Route::get('/api/user/image/{filename}', [UserController::class, 'getImage']);