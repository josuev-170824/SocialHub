<?php

namespace App\Services\OAuth;

use League\OAuth2\Client\Provider\AbstractProvider;
use League\OAuth2\Client\Token\AccessToken;
use League\OAuth2\Client\Tool\BearerAuthorizationTrait;
use Psr\Http\Message\ResponseInterface;

// Clase para autenticar con Facebook
class FacebookProvider extends AbstractProvider
{
    use BearerAuthorizationTrait;

    protected $state;

    // URL base para autorizar a los usuarios para Facebook
    public function getBaseAuthorizationUrl(): string
    {
        return 'https://www.facebook.com/v18.0/dialog/oauth';
    }

    // URL base para obtener el token de acceso para Facebook
    public function getBaseAccessTokenUrl(array $params): string
    {
        return 'https://graph.facebook.com/v18.0/oauth/access_token';
    }

    // URL para obtener los detalles del usuario para Facebook
    public function getResourceOwnerDetailsUrl(AccessToken $token): string
    {
        return 'https://graph.facebook.com/v18.0/me?fields=id,name,email,picture';
    }

    // Alcances por defecto para Facebook (leer perfil y correo electrónico)
    protected function getDefaultScopes(): array
    {
        return ['email', 'public_profile'];
    }

    // Verifica la respuesta de la API para Facebook
    protected function checkResponse(ResponseInterface $response, $data): void
    {
        if (isset($data['error'])) {
            throw new \Exception($data['error']['message'] ?? 'Error en Facebook API');
        }
    }

    // Crea el propietario del recurso para Facebook
    protected function createResourceOwner(array $response, AccessToken $token): array
    {
        return $response;
    }

    // Obtiene el estado de la sesión para Facebook
    public function getState(): string
    {
        if (!isset($this->state)) {
            $this->state = bin2hex(random_bytes(16));
        }
        return $this->state;
    }

    // Construye la URL de autorización para Facebook
    public function buildAuthorizationUrl(): string
    {
        $params = [
            'response_type' => 'code',
            'client_id' => $this->clientId,
            'redirect_uri' => $this->redirectUri,
            'scope' => implode(',', $this->getDefaultScopes()),
            'state' => $this->getState()
        ];
        
        return $this->getBaseAuthorizationUrl() . '?' . http_build_query($params);
    }
}