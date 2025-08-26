<?php

namespace App\Http\Controllers;

use App\Models\CuentaRedSocial;
use App\Services\SocialMedia\MastodonService;
use App\Services\SocialMedia\LinkedInService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Publication;

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
        \Log::info('PublicationController@store llamado');
        \Log::info('Datos recibidos:', $request->all());
    
        try {
            \Log::info('Iniciando validación...');
            
            $request->validate([
                'contenido' => 'required|string|max:500',
                'redes' => 'required|array|min:1',
                'redes.*' => 'in:linkedin,mastodon',
                'tipo_publicacion' => 'required|in:inmediata,programada',
                'fecha' => 'nullable|date|after_or_equal:today',
                'hora' => 'nullable|date_format:H:i',
            ]);
    
            \Log::info('Validación exitosa, procesando publicación...');
    
            $redesSeleccionadas = $request->redes;
            $contenido = $request->contenido;
            $tipoPublicacion = $request->tipo_publicacion;
            $fecha = $request->fecha;
            $hora = $request->hora;
            
            // Construir fecha_hora para publicaciones programadas
            $fechaHora = null;
            if ($tipoPublicacion === 'programada') {
                if (!$fecha || !$hora) {
                    throw new \Exception('La fecha y hora son requeridas para publicaciones programadas');
                }
                
                $fechaHora = $fecha . ' ' . $hora . ':00';
                
                // Validar que la fecha sea en el futuro
                if (strtotime($fechaHora) <= time()) {
                    throw new \Exception('La fecha y hora deben ser al menos 1 minuto en el futuro');
                }
            }
    
            \Log::info('Publicación solicitada:', [
                'contenido' => $contenido,
                'redes' => $redesSeleccionadas,
                'tipo' => $tipoPublicacion,
                'fecha_hora' => $fechaHora
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

            $publicacion = Publication::create([
                'user_id' => Auth::id(),
                'contenido' => $contenido,
                'redes' => $redesSeleccionadas,
                'tipo_publicacion' => $tipoPublicacion,
                'fecha_hora' => $fechaHora,
                'estado' => $tipoPublicacion === 'inmediata' ? 'completada' : 'pendiente',
                'resultados' => $resultados
            ]);

            \Log::info('Publicación guardada en BD con ID:', ['id' => $publicacion->id]);

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

    // Vista para editar una publicación
    public function edit(Publication $publication)
    {
        // Verificar que el usuario sea dueño de la publicación
        if ($publication->user_id !== Auth::id()) {
            abort(403, 'No tienes permiso para editar esta publicación');
        }

        // Solo permitir editar publicaciones programadas pendientes
        if ($publication->tipo_publicacion !== 'programada' || $publication->estado !== 'pendiente') {
            abort(403, 'Solo se pueden editar publicaciones programadas pendientes');
        }

        return view('publications.edit', compact('publication'));
    }

    // Actualizar una publicación
    public function update(Request $request, Publication $publication)
    {
        if ($publication->user_id !== Auth::id()) {
            abort(403, 'No tienes permiso para actualizar esta publicación');
        }

        if ($publication->tipo_publicacion !== 'programada' || $publication->estado !== 'pendiente') {
            abort(403, 'Solo se pueden editar publicaciones programadas pendientes');
        }

        $request->validate([
            'contenido' => 'required|string|max:500',
            'redes' => 'required|array|min:1',
            'redes.*' => 'in:linkedin,mastodon',
            'fecha' => 'required|date|after_or_equal:today',
            'hora' => 'required|date_format:H:i',
        ]);

        $fechaHora = $request->fecha . ' ' . $request->hora . ':00';
        
        if (strtotime($fechaHora) <= time()) {
            return redirect()->back()->with('error', 'La fecha y hora deben ser al menos 1 minuto en el futuro');
        }

        $publication->update([
            'contenido' => $request->contenido,
            'redes' => $request->redes,
            'fecha_hora' => $fechaHora,
        ]);

        return redirect()->route('schedules.index')->with('success', 'Publicación actualizada correctamente');
    }

    // Eliminar una publicación
    public function destroy(Publication $publication)
    {
        // vldar que el usuario sea dueño de la publicación
        if ($publication->user_id !== Auth::id()) {
            abort(403, 'No tienes permiso para eliminar esta publicación');
        }

        // permitir eliminar publicaciones programadas pendientes
        if ($publication->tipo_publicacion !== 'programada' || $publication->estado !== 'pendiente') {
            abort(403, 'Solo se pueden eliminar publicaciones programadas pendientes');
        }

        $publication->delete();

        return redirect()->route('schedules.index')->with('success', 'Publicación eliminada correctamente');
    }

    // Mostrar las publicaciones
    public function index()
    {
        $publicaciones = Publication::where('user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->paginate(10);
        
        return view('publications.index', compact('publicaciones'));
    }

    public function schedules()
    {
        $publicaciones = Publication::where('user_id', Auth::id())
            ->where('tipo_publicacion', 'programada')
            ->orderBy('fecha_hora', 'asc')
            ->get();
        
        return view('schedules.index', compact('publicaciones'));
    }
}