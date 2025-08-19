<?php

namespace App\Http\Controllers;

use App\Models\CuentaRedSocial;
use App\Services\OAuth\MastodonProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use League\OAuth2\Client\Provider\Exception\IdentityProviderException;

class ControladorAutenticacionMastodon extends Controller
{
    protected MastodonProvider $provider;

    public function __construct()
    {
        $this->provider = new MastodonProvider([
            'clientId' => config('services.mastodon.client_id'),
            'clientSecret' => config('services.mastodon.client_secret'),
            'redirectUri' => config('services.mastodon.redirect'),
            'instanceUrl' => config('services.mastodon.instance_url'),
        ]);
    }

    // Redirige al usuario a la página de autenticación de Mastodon
    public function redirigir()
    {
        $authUrl = $this->provider->buildAuthorizationUrl();
        session(['oauth2state' => $this->provider->getState()]);
        
        return redirect($authUrl);
    }

    // Maneja la respuesta de Mastodon (callback)
    public function callback(Request $request)
    {
        // Verifica que el estado de la sesión sea el mismo que el estado de la solicitud
        if ($request->get('state') !== session('oauth2state')) {
            session()->forget('oauth2state');
            return redirect()->route('user.settings')->with('error', 'Estado OAuth inválido');
        }

        // Intenta obtener el token de acceso
        try {
            $token = $this->provider->getAccessToken('authorization_code', [
                'code' => $request->get('code')
            ]);

            // Obtiene los detalles del usuario
            $user = $this->provider->getResourceOwner($token);
            $userData = is_array($user) ? $user : $user->toArray();

            // Guarda la cuenta de Mastodon
            $this->guardarCuentaMastodon($userData, $token);

            return redirect()->route('user.settings')->with('success', 'Cuenta de Mastodon vinculada exitosamente');

        } catch (IdentityProviderException $e) {
            return redirect()->route('user.settings')->with('error', 'Error al autenticar con Mastodon: ' . $e->getMessage());
        }
    }

    // Guarda la cuenta de Mastodon
    protected function guardarCuentaMastodon(array $userData, $token): void
    {
        $userId = Auth::id();
        
        // Guarda la cuenta de Mastodon (si ya existe, actualiza los datos)
        CuentaRedSocial::updateOrCreate(
            [
                'user_id' => $userId,
                'plataforma' => 'mastodon',
                'id_plataforma' => $userData['id']
            ],
            [
                'nombre_usuario' => '@' . $userData['username'] . '@' . parse_url($this->provider->getInstanceUrl(), PHP_URL_HOST),
                'token_acceso' => $token->getToken(),
                'token_refresco' => $token->getRefreshToken(),
                'token_expiracion' => $token->getExpires() ? now()->addSeconds($token->getExpires()) : null,
                'datos_adicionales' => [
                    'username' => $userData['username'],
                    'display_name' => $userData['display_name'],
                    'avatar' => $userData['avatar'],
                    'instance_url' => $this->provider->getInstanceUrl()
                ],
                'activa' => true
            ]
        );
    }

    // Desvincula la cuenta de Mastodon
    public function desvincular()
    {
        // Obtiene el ID del usuario autenticado
        $userId = Auth::id();
        
        // Desvincula la cuenta de Mastodon
        CuentaRedSocial::where('user_id', $userId)
            ->where('plataforma', 'mastodon')
            ->update(['activa' => false]);

        return redirect()->back()->with('success', 'Cuenta de Mastodon desvinculada');
    }
}