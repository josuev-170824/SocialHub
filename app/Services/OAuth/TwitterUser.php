<?php

namespace App\Services\OAuth;

use League\OAuth2\Client\Provider\ResourceOwnerInterface;

// Clase para obtener los detalles del usuario de Twitter
class TwitterUser implements ResourceOwnerInterface
{
    protected array $response;

    // Constructor para inicializar la respuesta
    public function __construct(array $response)
    {
        $this->response = $response;
    }

    // Método para obtener el ID del usuario
    public function getId(): string
    {
        return $this->response['data']['id'] ?? '';
    }

    // Método para obtener el nombre de usuario
    public function getUsername(): string
    {
        return $this->response['data']['username'] ?? '';
    }

    // Método para obtener el nombre del usuario
    public function getName(): string
    {
        return $this->response['data']['name'] ?? '';
    }

    // Método para convertir la respuesta a un array
    public function toArray(): array
    {
        return $this->response;
    }
}