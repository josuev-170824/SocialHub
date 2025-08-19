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

    // Twitter
    Route::get('/auth/twitter', [App\Http\Controllers\ControladorAutenticacionTwitter::class, 'redirigir'])
        ->name('auth.twitter');
    Route::get('/auth/twitter/callback', [App\Http\Controllers\ControladorAutenticacionTwitter::class, 'callback'])
        ->name('auth.twitter.callback');
    Route::post('/auth/twitter/desvincular', [App\Http\Controllers\ControladorAutenticacionTwitter::class, 'desvincular'])
        ->name('auth.twitter.desvincular');

    // LinkedIn
    Route::get('/auth/linkedin', [App\Http\Controllers\ControladorAutenticacionLinkedIn::class, 'redirigir'])
    ->name('auth.linkedin');
    Route::get('/auth/linkedin/callback', [App\Http\Controllers\ControladorAutenticacionLinkedIn::class, 'callback'])
    ->name('auth.linkedin.callback');
    Route::post('/auth/linkedin/desvincular', [App\Http\Controllers\ControladorAutenticacionLinkedIn::class, 'desvincular'])
    ->name('auth.linkedin.desvincular');
    
    // Ruta para configuración de usuario
    Route::get('/settings', function () {
        // Obtiene la cuenta de Twitter del usuario autenticado
        $cuentaTwitter = Auth::user()->cuentasRedesSociales()
            ->where('plataforma', 'twitter')
            ->where('activa', true)
            ->first();
        
        // Obtiene la cuenta de Facebook del usuario autenticado
        $cuentaFacebook = Auth::user()->cuentasRedesSociales()
            ->where('plataforma', 'facebook')
            ->where('activa', true)
            ->first();
        
        // Obtiene la cuenta de LinkedIn del usuario autenticado
        $cuentaLinkedIn = Auth::user()->cuentasRedesSociales()
            ->where('plataforma', 'linkedin')
            ->where('activa', true)
            ->first();
        
        return view('auth.settings', compact('cuentaTwitter', 'cuentaFacebook', 'cuentaLinkedIn'));
    })->name('user.settings');
    
    // Rutas que SÍ requieren verificación 2FA
    Route::middleware(\App\Http\Middleware\Verificar2FA::class)->group(function () {
        Route::get('/dashboard', function () {
            return view('dashboard');
        })->name('dashboard');
        
        Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    });
});