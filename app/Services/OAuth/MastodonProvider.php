<?php

namespace App\Services\OAuth;

use League\OAuth2\Client\Provider\AbstractProvider;
use League\OAuth2\Client\Token\AccessToken;
use League\OAuth2\Client\Tool\BearerAuthorizationTrait;
use Psr\Http\Message\ResponseInterface;

// Clase para autenticar con Mastodon
class MastodonProvider extends AbstractProvider
{
    use BearerAuthorizationTrait;

    protected $state;
    protected $instanceUrl;

    public function __construct(array $options = [])
    {
        parent::__construct($options);
        $this->instanceUrl = $options['instanceUrl'] ?? 'https://mastodon.social';
    }

    // URL base para autorizar a los usuarios para Mastodon
    public function getBaseAuthorizationUrl(): string
    {
        return $this->instanceUrl . '/oauth/authorize';
    }

    // URL base para obtener el token de acceso para Mastodon
    public function getBaseAccessTokenUrl(array $params): string
    {
        return $this->instanceUrl . '/oauth/token';
    }

    // URL para obtener los detalles del usuario para Mastodon
    public function getResourceOwnerDetailsUrl(AccessToken $token): string
    {
        return $this->instanceUrl . '/api/v1/accounts/verify_credentials';
    }

    // Alcances por defecto para Mastodon (leer y escribir)
    protected function getDefaultScopes(): array
    {
        return ['read', 'write'];
    }

    // Verifica la respuesta de la API para Mastodon
    protected function checkResponse(ResponseInterface $response, $data): void
    {
        if ($response->getStatusCode() >= 400) {
            throw new \Exception($data['error'] ?? 'Error en Mastodon API');
        }
    }

    // Crea el propietario del recurso para Mastodon
    protected function createResourceOwner(array $response, AccessToken $token): array
    {
        return $response;
    }

    // Obtiene el estado de la sesión para Mastodon
    public function getState(): string
    {
        if (!isset($this->state)) {
            $this->state = bin2hex(random_bytes(16));
        }
        return $this->state;
    }

    // Construye la URL de autorización para Mastodon
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

    // Obtiene la URL de la instancia de Mastodon
    public function getInstanceUrl(): string
    {
        return $this->instanceUrl;
    }
}