<?php

use App\Http\Controllers\ImageController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Middleware\ApiAuthMiddleware;

Route::get('/', function () {
    return view('welcome');
});

// Rutas de UserController
Route::get('/prueba', [UserController::class, 'prueba']); 
Route::post('/api/register', [UserController::class, 'register']);
Route::post('/api/login', [UserController::class, 'login']);
Route::get('/api/user/image/{filename}', [UserController::class, 'getImage']);
Route::get(('/api/user/detail/{id}'), [UserController::class, 'detail']);

// Rutas con el middleware de autenticación
Route::middleware([ApiAuthMiddleware::class])->group(function () {
    Route::post('/api/image/store', [ImageController::class, 'store']);
    Route::post('/api/user/update', [UserController::class, 'update']);
});

// Rutas de ImageController
Route::get('/api/image/{filename}', [ImageController::class, 'getImage']);