<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\GoogleAuthController;
use App\Http\Controllers\AuthController;

// Rutas de autenticación
Route::get('/', function () {
    return view('auth.login');
});

Route::get('/register', function () {
    return view('auth.register');
});

// Rutas de autenticación con Google
Route::get('/auth/google/redirect', [GoogleAuthController::class, 'redirect'])->name('google.redirect');
Route::get('/auth/google/callback', [GoogleAuthController::class, 'callback'])->name('google.callback');

// Rutas de autenticación tradicional
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register']);

// Rutas protegidas (solo accesibles si el usuario está autenticado)
Route::middleware('auth')->group(function () {
    // Ruta para mostrar el dashboard
    Route::get('/dashboard', function () {
        // Muestra el dashboard
        return view('dashboard');
    })->name('dashboard');
    // Ruta para cerrar sesión
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
});