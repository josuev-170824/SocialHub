<?php

namespace App\Http\Controllers;

use App\Models\CuentaRedSocial;
use App\Services\SocialMedia\MastodonService;
use App\Services\SocialMedia\LinkedInService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PublicationController extends Controller
{
    // Vista para crear una publicación
    public function create()
    {
        // Obtiene las cuentas de LinkedIn y Mastodon del usuario autenticado
        $cuentaLinkedIn = Auth::user()->cuentasRedesSociales()
            ->where('plataforma', 'linkedin')
            ->where('activa', true)
            ->first();
        
        $cuentaMastodon = Auth::user()->cuentasRedesSociales()
            ->where('plataforma', 'mastodon')
            ->where('activa', true)
            ->first();

        return view('publications.create', compact('cuentaLinkedIn', 'cuentaMastodon'));
    }

    // Guardar una publicación
    public function store(Request $request)
    {
        // Debug: Ver si llega aquí
        \Log::info('PublicationController@store llamado');
        \Log::info('Datos recibidos:', $request->all());
    
        try {
            // Valida los datos del formulario
            \Log::info('Iniciando validación...');
            
            $request->validate([
                'contenido' => 'required|string|max:500',
                'redes' => 'required|array|min:1',
                'redes.*' => 'in:linkedin,mastodon',
                'tipo_publicacion' => 'required|in:inmediata,programada',
                'fecha_hora' => 'nullable|date|after:now',
            ]);
    
            \Log::info('Validación exitosa, procesando publicación...');
    
            $redesSeleccionadas = $request->redes;
            $contenido = $request->contenido;
            $tipoPublicacion = $request->tipo_publicacion;
            $fechaHora = $request->fecha_hora;
    
            // Validación manual para fecha programada
            if ($tipoPublicacion === 'programada' && !$fechaHora) {
                throw new \Exception('La fecha y hora son requeridas para publicaciones programadas');
            }
    
            // Debug: Log de lo que se está procesando
            \Log::info('Publicación solicitada:', [
                'contenido' => $contenido,
                'redes' => $redesSeleccionadas,
                'tipo' => $tipoPublicacion,
                'fecha' => $fechaHora
            ]);
    
            $resultados = [];
    
            foreach ($redesSeleccionadas as $red) {
                try {
                    \Log::info("Procesando publicación en: $red");
                    
                    switch ($red) {
                        case 'mastodon':
                            \Log::info('Creando servicio Mastodon...');
                            $mastodonService = new MastodonService();
                            $resultado = $mastodonService->publicar($contenido, $tipoPublicacion, $fechaHora);
                            $resultados[$red] = $resultado;
                            \Log::info('Resultado Mastodon:', $resultado);
                            break;
                        
                        case 'linkedin':
                            \Log::info('Creando servicio LinkedIn...');
                            $linkedInService = new LinkedInService();
                            $resultado = $linkedInService->publicar($contenido, $tipoPublicacion, $fechaHora);
                            $resultados[$red] = $resultado;
                            \Log::info('Resultado LinkedIn:', $resultado);
                            break;
                    }
                } catch (\Exception $e) {
                    \Log::error("Error en publicación $red:", ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
                    $resultados[$red] = ['error' => $e->getMessage()];
                }
            }
    
            \Log::info('Resultados finales:', $resultados);
    
            // Mostrar resultados detallados
            $mensaje = "Publicación procesada:\n";
            foreach ($resultados as $red => $resultado) {
                if (isset($resultado['error'])) {
                    $mensaje .= "❌ $red: " . $resultado['error'] . "\n";
                } else {
                    $mensaje .= "✅ $red: " . $resultado['message'] . "\n";
                }
            }
    
            \Log::info('Redirigiendo con mensaje:', ['mensaje' => $mensaje]);
    
            return redirect()->route('dashboard')->with('success', $mensaje);
    
        } catch (\Exception $e) {
            \Log::error('Error general en PublicationController:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return redirect()->back()->with('error', 'Error al procesar la publicación: ' . $e->getMessage());
        }
    }
}