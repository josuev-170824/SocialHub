<?php

namespace App\Services\SocialMedia;

use App\Models\CuentaRedSocial;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Auth;

class LinkedInService
{
    // Publica un mensaje en LinkedIn
    public function publicar(string $contenido, string $tipoPublicacion, ?string $fechaHora = null)
    {
        // Obtiene la cuenta de LinkedIn del usuario autenticado
        $cuenta = Auth::user()->cuentasRedesSociales()
            ->where('plataforma', 'linkedin')
            ->where('activa', true)
            ->first();

        if (!$cuenta) {
            throw new \Exception('Debe vincular una cuenta de LinkedIn para poder publicar');
        }

        // Obtiene el token de acceso de la cuenta de LinkedIn
        $accessToken = $cuenta->token_acceso;

        // Obtiene el perfil del usuario
        $profileUrl = 'https://api.linkedin.com/v2/userinfo';
        
        $profileResponse = Http::withHeaders([
            'Authorization' => 'Bearer ' . $accessToken,
            'Content-Type' => 'application/json',
        ])->get($profileUrl);

        if (!$profileResponse->successful()) {
            throw new \Exception('Error al obtener perfil de LinkedIn: ' . $profileResponse->body());
        }

        $profileData = $profileResponse->json();
        $authorUrn = 'urn:li:person:' . $profileData['sub'];

        // Construye la URL para publicar en LinkedIn
        $postUrl = 'https://api.linkedin.com/v2/ugcPosts';
        
        $postData = [
            'author' => $authorUrn,
            'lifecycleState' => 'PUBLISHED',
            'specificContent' => [
                'com.linkedin.ugc.ShareContent' => [
                    'shareCommentary' => [
                        'text' => $contenido
                    ],
                    'shareMediaCategory' => 'NONE'
                ]
            ],
            'visibility' => [
                'com.linkedin.ugc.MemberNetworkVisibility' => 'PUBLIC'
            ]
        ];

        // Hace la solicitud a la API de LinkedIn para publicar
        $postResponse = Http::withHeaders([
            'Authorization' => 'Bearer ' . $accessToken,
            'Content-Type' => 'application/json',
            'X-Restli-Protocol-Version' => '2.0.0',
            'LinkedIn-Version' => '202308'
        ])->post($postUrl, $postData);

        // Valida si la publicación fue exitosa
        if ($postResponse->successful()) {
            $responseData = $postResponse->json();
            return [
                'success' => true,
                'id' => $responseData['id'] ?? 'unknown',
                'message' => 'Publicación exitosa en LinkedIn'
            ];
        } else {
            throw new \Exception('Error al publicar en LinkedIn: ' . $postResponse->body());
        }
    }
}