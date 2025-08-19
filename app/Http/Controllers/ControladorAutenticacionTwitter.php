<?php

namespace App\Http\Controllers;

use App\Models\CuentaRedSocial;
use App\Services\OAuth\TwitterProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use League\OAuth2\Client\Provider\Exception\IdentityProviderException;

class ControladorAutenticacionTwitter extends Controller
{
    // Proveedor de autenticación para Twitter (para obtener el token de acceso)
    protected TwitterProvider $provider;

    public function __construct()
    {
        $this->provider = new TwitterProvider([
            'clientId' => config('services.twitter.client_id'),
            'clientSecret' => config('services.twitter.client_secret'),
            'redirectUri' => config('services.twitter.redirect'),
        ]);
    }

    // Redirige al usuario a la página de autenticación de Twitter
    public function redirigir()
    {
        // Obtiene la URL de autenticación de Twitter
        $authUrl = $this->provider->getAuthorizationUrl();
        // Guarda el estado de la sesión para verificar la autenticación
        session(['oauth2state' => $this->provider->getState()]);
        // Redirige al usuario a la página de autenticación de Twitter 
        return redirect($authUrl);
    }

    // Maneja la respuesta de Twitter (callback)
    public function callback(Request $request)
    {
        // Verifica que el estado de la sesión sea el mismo que el estado de la solicitud
        if ($request->get('state') !== session('oauth2state')) {
            session()->forget('oauth2state');
            return redirect()->route('home')->with('error', 'Estado OAuth inválido');
        }
        try {
            // Intenta obtener el token de acceso 
            $token = $this->provider->getAccessToken('authorization_code', [

                'code' => $request->get('code')
            ]);

            // Obtiene los detalles del usuario
            $user = $this->provider->getResourceOwner($token);
            $userData = $user->toArray();
            
            // Guarda la cuenta de Twitter
            $this->guardarCuentaTwitter($userData, $token);

            return redirect()->route('home')->with('success', 'Cuenta de Twitter vinculada exitosamente');

        } catch (IdentityProviderException $e) {
            return redirect()->route('home')->with('error', 'Error al autenticar con Twitter: ' . $e->getMessage());
        }
    }

    // Guarda la cuenta de Twitter
    protected function guardarCuentaTwitter(array $userData, $token): void
    {
        // Obtiene el ID del usuario autenticado 
        $userId = Auth::id();
        
        // Guarda la cuenta de Twitter (si ya existe, actualiza los datos)
        CuentaRedSocial::updateOrCreate(
            [
                'user_id' => $userId,
                'plataforma' => 'twitter',
                'id_plataforma' => $userData['data']['id']
            ],
            [
                'nombre_usuario' => $userData['data']['username'],
                'token_acceso' => $token->getToken(),
                'token_refresco' => $token->getRefreshToken(),
                'token_expiracion' => $token->getExpires() ? now()->addSeconds($token->getExpires()) : null,
                'datos_adicionales' => [
                    'name' => $userData['data']['name'] ?? '',
                    'profile_image_url' => $userData['data']['profile_image_url'] ?? ''
                ],
                'activa' => true
            ]
        );
    }

    // Desvincula la cuenta de Twitter
    public function desvincular()
    {
        // Obtiene el ID del usuario autenticado 
        $userId = Auth::id();
        
        // Desvincula la cuenta de Twitter
        CuentaRedSocial::where('user_id', $userId)
            ->where('plataforma', 'twitter')
            ->update(['activa' => false]);

        return redirect()->back()->with('success', 'Cuenta de Twitter desvinculada');
    }
}