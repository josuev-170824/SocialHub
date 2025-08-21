<?php

namespace App\Services\SocialMedia;

use App\Models\CuentaRedSocial;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Auth;

class MastodonService
{
    // Publica un mensaje en Mastodon
    public function publicar(string $contenido, string $tipoPublicacion, ?string $fechaHora = null)
    {
        // Obtiene la cuenta de Mastodon del usuario autenticado
        $cuenta = Auth::user()->cuentasRedesSociales()
            ->where('plataforma', 'mastodon')
            ->where('activa', true)
            ->first();

        // Verifica que la cuenta de Mastodon esté conectada
        if (!$cuenta) {
            throw new \Exception('Debe vincular una cuenta de Mastodon para poder publicar');
        }

        // Obtiene la URL de la instancia de Mastodon y el token de acceso
        $instanceUrl = $cuenta->datos_adicionales['instance_url'];
        $accessToken = $cuenta->token_acceso;

        // Construye la URL para publicar en Mastodon
        $url = $instanceUrl . '/api/v1/statuses';
        
        // Construye los datos para la publicación
        $data = [
            'status' => $contenido,
            'visibility' => 'public',
        ];

        // Hace la solicitud a la API de Mastodon para publicar
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $accessToken,
            'Content-Type' => 'application/json',
        ])->post($url, $data);

        // Verifica si la publicación fue exitosa
        if ($response->successful()) {
            return [
                'success' => true,
                'id' => $response->json('id'),
                'url' => $response->json('url'),
                'message' => 'Publicación exitosa en Mastodon'
            ];
        } else {
            throw new \Exception('Error al publicar en Mastodon: ' . $response->body());
        }
    }
}