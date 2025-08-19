<?php

namespace App\Http\Controllers;

use App\Models\CuentaRedSocial;
use App\Services\OAuth\FacebookProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use League\OAuth2\Client\Provider\Exception\IdentityProviderException;

class ControladorAutenticacionFacebook extends Controller
{
    protected FacebookProvider $provider;

    public function __construct()
    {
        $this->provider = new FacebookProvider([
            'clientId' => config('services.facebook.client_id'),
            'clientSecret' => config('services.facebook.client_secret'),
            'redirectUri' => config('services.facebook.redirect'),
        ]);
    }

    // Redirige al usuario a la página de autenticación de Facebook
    public function redirigir()
    {
        // Obtiene la URL de autenticación de Facebook
        $authUrl = $this->provider->buildAuthorizationUrl();
        // Guarda el estado de la sesión para verificar la autenticación
        session(['oauth2state' => $this->provider->getState()]);
        // Redirige al usuario a la página de autenticación de Facebook
        return redirect($authUrl);
    }

    // Maneja la respuesta de Facebook (callback)
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

            // Guarda la cuenta de Facebook
            $this->guardarCuentaFacebook($userData, $token);

            return redirect()->route('home')->with('success', 'Cuenta de Facebook vinculada exitosamente');

        } catch (IdentityProviderException $e) {
            return redirect()->route('home')->with('error', 'Error al autenticar con Facebook: ' . $e->getMessage());
        }
    }

    // Guarda la cuenta de Facebook
    protected function guardarCuentaFacebook(array $userData, $token): void
    {
        // Obtiene el ID del usuario autenticado
        $userId = Auth::id();
        
        // Guarda la cuenta de Facebook (si ya existe, actualiza los datos)
        CuentaRedSocial::updateOrCreate(
            [
                'user_id' => $userId,
                'plataforma' => 'facebook',
                'id_plataforma' => $userData['id']
            ],
            [
                'nombre_usuario' => $userData['name'],
                'token_acceso' => $token->getToken(),
                'token_refresco' => $token->getRefreshToken(),
                'token_expiracion' => $token->getExpires() ? now()->addSeconds($token->getExpires()) : null,
                'datos_adicionales' => [
                    'email' => $userData['email'] ?? '',
                    'picture' => $userData['picture']['data']['url'] ?? ''
                ],
                'activa' => true
            ]
        );
    }

    // Desvincula la cuenta de Facebook
    public function desvincular()
    {
        // Obtiene el ID del usuario autenticado
        $userId = Auth::id();
        
        CuentaRedSocial::where('user_id', $userId)
            ->where('plataforma', 'facebook')
            ->update(['activa' => false]);

        return redirect()->back()->with('success', 'Cuenta de Facebook desvinculada');
    }
}