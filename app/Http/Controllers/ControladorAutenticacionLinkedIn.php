<?php

namespace App\Http\Controllers;

use App\Models\CuentaRedSocial;
use App\Services\OAuth\LinkedInProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use League\OAuth2\Client\Provider\Exception\IdentityProviderException;

class ControladorAutenticacionLinkedIn extends Controller
{
    protected LinkedInProvider $provider;

    // Constructor que inicializa el proveedor de LinkedIn con las credenciales de configuración
    public function __construct()
    {
        $this->provider = new LinkedInProvider([
            'clientId' => config('services.linkedin.client_id'),
            'clientSecret' => config('services.linkedin.client_secret'),
            'redirectUri' => config('services.linkedin.redirect'),
        ]);
    }

    // Redirige al usuario a la página de autenticación de LinkedIn
    public function redirigir()
    {
        // Obtiene la URL de autenticación de LinkedIn
        $authUrl = $this->provider->buildAuthorizationUrl();
        // Guarda el estado de la sesión para verificar la autenticación
        session(['oauth2state' => $this->provider->getState()]);
        
        return redirect($authUrl);
    }

    // Maneja la respuesta de LinkedIn (callback)
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

            // Guarda la cuenta de LinkedIn
            $this->guardarCuentaLinkedIn($userData, $token);

            return redirect()->route('home')->with('success', 'Cuenta de LinkedIn vinculada exitosamente');

        } catch (IdentityProviderException $e) {
            return redirect()->route('home')->with('error', 'Error al autenticar con LinkedIn: ' . $e->getMessage());
        }
    }

    // Guarda la cuenta de LinkedIn
    protected function guardarCuentaLinkedIn(array $userData, $token): void
    {
        // Obtiene el ID del usuario autenticado
        $userId = Auth::id();
        
        // Guarda la cuenta de LinkedIn (si ya existe, actualiza los datos)
        CuentaRedSocial::updateOrCreate(
            [
                'user_id' => $userId,
                'plataforma' => 'linkedin',
                'id_plataforma' => $userData['id']
            ],
            [
                'nombre_usuario' => $userData['localizedFirstName'] . ' ' . $userData['localizedLastName'],
                'token_acceso' => $token->getToken(),
                'token_refresco' => $token->getRefreshToken(),
                'token_expiracion' => $token->getExpires() ? now()->addSeconds($token->getExpires()) : null,
                'datos_adicionales' => [
                    'firstName' => $userData['localizedFirstName'] ?? '',
                    'lastName' => $userData['localizedLastName'] ?? '',
                    'profilePicture' => $userData['profilePicture'] ?? ''
                ],
                'activa' => true
            ]
        );
    }

    // Desvincula la cuenta de LinkedIn
    public function desvincular()
    {
        // Obtiene el ID del usuario autenticado
        $userId = Auth::id();
        
        // Desvincula la cuenta de LinkedIn
        CuentaRedSocial::where('user_id', $userId)
            ->where('plataforma', 'linkedin')
            ->update(['activa' => false]);

        return redirect()->back()->with('success', 'Cuenta de LinkedIn desvinculada');
    }
}