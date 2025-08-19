<?php

namespace App\Services\OAuth;

use League\OAuth2\Client\Provider\AbstractProvider;
use League\OAuth2\Client\Token\AccessToken;
use League\OAuth2\Client\Tool\BearerAuthorizationTrait;
use Psr\Http\Message\ResponseInterface;

class LinkedInProvider extends AbstractProvider
{
    use BearerAuthorizationTrait;

    protected $state;

    // URL base para autorizar a los usuarios para LinkedIn
    public function getBaseAuthorizationUrl(): string
    {
        return 'https://www.linkedin.com/oauth/v2/authorization';
    }

    // URL base para obtener el token de acceso para LinkedIn
    public function getBaseAccessTokenUrl(array $params): string
    {
        return 'https://www.linkedin.com/oauth/v2/accessToken';
    }

    // URL para obtener los detalles del usuario para LinkedIn
    public function getResourceOwnerDetailsUrl(AccessToken $token): string
    {
        return 'https://api.linkedin.com/v2/me';
    }

    // Alcances por defecto para LinkedIn (leer perfil)
    protected function getDefaultScopes(): array
    {
        return ['r_liteprofile'];
    }

    // Verifica la respuesta de la API para LinkedIn
    protected function checkResponse(ResponseInterface $response, $data): void
    {
        if ($response->getStatusCode() >= 400) {
            throw new \Exception($data['message'] ?? 'Error en LinkedIn API');
        }
    }

    // Crea el propietario del recurso para LinkedIn
    protected function createResourceOwner(array $response, AccessToken $token): array
    {
        return $response;
    }

    // Obtiene el estado de la sesión para LinkedIn
    public function getState(): string
    {
        if (!isset($this->state)) {
            $this->state = bin2hex(random_bytes(16));
        }
        return $this->state;
    }

    // Construye la URL de autorización para LinkedIn
    public function buildAuthorizationUrl(): string
    {
        $params = [
            'response_type' => 'code',
            'client_id' => $this->clientId,
            'redirect_uri' => $this->redirectUri,
            'scope' => implode(' ', $this->getDefaultScopes()),
            'state' => $this->getState()
        ];
        
        return $this->getBaseAuthorizationUrl() . '?' . http_build_query($params);
    }
}