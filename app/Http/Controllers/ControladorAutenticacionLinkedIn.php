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
        
        \Log::info('LinkedIn redirect URL: ' . $authUrl);
        
        return redirect($authUrl);
    }

    // Maneja la respuesta de LinkedIn (callback)
    public function callback(Request $request)
    {
        // Debug: Ver qué está llegando
        \Log::info('LinkedIn callback data:', $request->all());
        
        // Verifica que el estado de la sesión sea el mismo que el estado de la solicitud
        if ($request->get('state') !== session('oauth2state')) {
            session()->forget('oauth2state');
            return redirect()->route('user.settings')->with('error', 'Estado OAuth inválido');
        }

        // Verifica que el código esté presente
        if (!$request->has('code')) {
            \Log::error('LinkedIn callback missing code parameter');
            return redirect()->route('user.settings')->with('error', 'Error: Código de autorización no recibido de LinkedIn');
        }

        try {
            // Intenta obtener el token de acceso
            $token = $this->provider->getAccessToken('authorization_code', [
                'code' => $request->get('code')
            ]);

            // Obtiene los detalles del usuario
            $user = $this->provider->getResourceOwner($token);
            $userData = is_array($user) ? $user : $user->toArray();

            // Guarda la cuenta de LinkedIn
            $this->guardarCuentaLinkedIn($userData, $token);

            return redirect()->route('user.settings')->with('success', 'Cuenta de LinkedIn vinculada exitosamente');

        } catch (IdentityProviderException $e) {
            \Log::error('LinkedIn OAuth error: ' . $e->getMessage());
            return redirect()->route('user.settings')->with('error', 'Error al autenticar con LinkedIn: ' . $e->getMessage());
        }
    }

    // Guarda la cuenta de LinkedIn
    protected function guardarCuentaLinkedIn(array $userData, $token): void
    {
        $userId = Auth::id();
                
        // Validar y corregir la fecha de expiración
        $tokenExpiracion = null;
        if ($token->getExpires()) {
            $expiresAt = now()->addSeconds($token->getExpires());
            // Verificar que la fecha no sea muy lejana (máximo 10 años)
            if ($expiresAt->year <= now()->addYears(10)->year) {
                $tokenExpiracion = $expiresAt;
            }
        }
        
        // Guarda la cuenta de LinkedIn
        CuentaRedSocial::updateOrCreate(
            [
                'user_id' => $userId,
                'plataforma' => 'linkedin',
                'id_plataforma' => $userData['sub'] ?? $userData['id']
            ],
            [
                'nombre_usuario' => $userData['name'] ?? 'Usuario LinkedIn',
                'token_acceso' => $token->getToken(),
                'token_refresco' => $token->getRefreshToken(),
                'token_expiracion' => $tokenExpiracion,
                'datos_adicionales' => [
                    'name' => $userData['name'] ?? '',
                    'email' => $userData['email'] ?? '',
                    'picture' => $userData['picture'] ?? ''
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