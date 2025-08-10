<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use PragmaRX\Google2FA\Google2FA;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\Image\SvgImageBackEnd;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Writer;

// Controlador para la gestión de 2FA (Segundo Factor de Autenticación)
class Google2FAController extends Controller
{
    // Constructor para asegurar que el usuario esté autenticado 
    public function __construct()
    {
        // Asegura que el usuario esté autenticado
        $this->middleware('auth');
    }

    // Método para mostrar la pantalla de configuración de 2FA
    public function showSetup()
    {
        // Obtener el usuario autenticado
        $user = Auth::user();
        
        // Verifica si el usuario ya tiene 2FA activo
        if ($user->google2fa_enabled) {
            return redirect('/dashboard')->with('info', '2FA ya está activado');
        }

        // Genera un nuevo secreto de 2FA
        $google2fa = new Google2FA();
        $secret = $google2fa->generateSecretKey();

        // Actualiza el secreto de 2FA en la base de datos
        $user->update(['google2fa_secret' => $secret]);
        
        // Genera la URL del código QR para Google Authenticator :)
        $qrCodeUrl = $google2fa->getQRCodeUrl(
            'SocialHub Manager',
            $user->email,
            $secret
        );

        // Muestra la pantalla de configuración de 2FA
        return view('auth.setup-2fa', compact('secret', 'qrCodeUrl'));
    }

    // Método para activar el 2FA
    public function enable(Request $request)
    {
        // Valida el código de verificación
        $request->validate([
            'code' => 'required|string|size:6'
        ]);

        // Obtener el usuario autenticado
        $user = Auth::user();
        $google2fa = new Google2FA();
        
        // Valida el código de verificación
        if ($google2fa->verifyKey($user->google2fa_secret, $request->code)) {
            $user->update([
                'google2fa_enabled' => true,
                'google2fa_enabled_at' => now()
            ]);
            
            // Si el código es válido, actualiza el estado del 2FA
            return redirect('/dashboard')->with('success', '2FA activado correctamente');
        }

        // Si el código es invlido, muestra mensaje de error
        return back()->withErrors(['code' => 'Código inválido']);
    }

    // Método para desactivar el 2FA
    public function disable(Request $request)
    {
        // Valida el código de verificación
        $request->validate([
            'code' => 'required|string|size:6'
        ]);

        // Obtener el usuario autenticado
        $user = Auth::user();
        $google2fa = new Google2FA();
        
        // Verificar el código de verificación
        if ($google2fa->verifyKey($user->google2fa_secret, $request->code)) {
            $user->update([
                'google2fa_enabled' => false,
                'google2fa_enabled_at' => null
            ]);
            
            return redirect('/dashboard')->with('success', '2FA desactivado correctamente');
        }

        return back()->withErrors(['code' => 'Código inválido']);
    }

    // Método para verificar el 2FA
    public function verify(Request $request)
    {
        // Validar el código de verificación
        $request->validate([
            'code' => 'required|string|size:6'
        ]);

        // Obtener el usuario autenticado
        $user = Auth::user();
        
        // Valida si el usuario tiene un secreto de 2FA
        if (!$user->google2fa_secret) {
            return redirect('/dashboard')->with('info', '2FA no está configurado');
        }
        
        $google2fa = new Google2FA();
        
        // Valida el código de verificación
        if ($google2fa->verifyKey($user->google2fa_secret, $request->code)) {
            session(['2fa_verified' => true]);
            return redirect()->intended('/dashboard');
        }

        // Si el código es invlido, muestra mensaje de error
        return back()->withErrors(['code' => 'Código 2FA inválido']);
    }

    // Método para mostrar la pantalla de verificación de 2FA
    public function showVerification()
    {
        // Verificar si el usuario ya tiene 2FA activo
        $user = Auth::user();
        if (!$user->google2fa_enabled) {
            return redirect('/dashboard')->with('info', '2FA no está activado');
        }
        
        // Verificar si el usuario ya ha verificado el 2FA
        if (session('2fa_verified')) {
            return redirect('/dashboard')->with('info', '2FA ya ha sido verificado');
        }
        
        return view('auth.verify-2fa');
    }
}