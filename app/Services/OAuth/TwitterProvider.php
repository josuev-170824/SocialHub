<?php

namespace App\Services\OAuth;

use League\OAuth2\Client\Provider\AbstractProvider;
use League\OAuth2\Client\Token\AccessToken;
use League\OAuth2\Client\Tool\BearerAuthorizationTrait;
use Psr\Http\Message\ResponseInterface;

class TwitterProvider extends AbstractProvider
{
    use BearerAuthorizationTrait;

    protected $state;

    public function getBaseAuthorizationUrl(): string
    {
        return 'https://twitter.com/i/oauth2/authorize';
    }

    public function getBaseAccessTokenUrl(array $params): string
    {
        return 'https://api.twitter.com/2/oauth2/token';
    }

    public function getResourceOwnerDetailsUrl(AccessToken $token): string
    {
        return 'https://api.twitter.com/2/users/me';
    }

    protected function getDefaultScopes(): array
    {
        return [];
    }

    protected function checkResponse(ResponseInterface $response, $data): void
    {
        if ($response->getStatusCode() >= 400) {
            throw new \Exception($data['message'] ?? 'Error en Twitter API');
        }
    }

    protected function createResourceOwner(array $response, AccessToken $token): array
    {
        return $response;
    }

    public function getState(): string
    {
        if (!isset($this->state)) {
            $this->state = bin2hex(random_bytes(16));
        }
        return $this->state;
    }

    public function buildAuthorizationUrl(): string
    {
        $params = [
            'response_type' => 'code',
            'client_id' => $this->clientId,
            'redirect_uri' => $this->redirectUri,
            'state' => $this->getState()
        ];
        
        return $this->getBaseAuthorizationUrl() . '?' . http_build_query($params);
    }
}