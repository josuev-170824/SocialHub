<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\GoogleAuthController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Google2FAController;

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

// Rutas protegidas (accesibles si el usuario está autenticado)
Route::middleware('auth')->group(function () {
    // Rutas de 2FA (no requieren verificación 2FA)
    Route::get('/2fa/setup', [Google2FAController::class, 'showSetup'])->name('2fa.setup');
    Route::post('/2fa/enable', [Google2FAController::class, 'enable'])->name('2fa.enable');
    Route::post('/2fa/disable', [Google2FAController::class, 'disable'])->name('2fa.disable');
    Route::get('/2fa/verify', [Google2FAController::class, 'showVerification'])->name('2fa.verify.show');
    Route::post('/2fa/verify', [Google2FAController::class, 'verify'])->name('2fa.verify.post');
    
    // Rutas que SÍ requieren verificación 2FA
    Route::middleware(\App\Http\Middleware\Verificar2FA::class)->group(function () {
        Route::get('/dashboard', function () {
            return view('dashboard');
        })->name('dashboard');
        
        Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    });
});