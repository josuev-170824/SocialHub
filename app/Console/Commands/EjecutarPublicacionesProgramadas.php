<?php

namespace App\Console\Commands;

use App\Models\Publication;
use App\Services\SocialMedia\MastodonService;
use App\Services\SocialMedia\LinkedInService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class EjecutarPublicacionesProgramadas extends Command
{
    protected $signature = 'publicaciones:ejecutar-programadas';
    protected $description = 'Ejecuta las publicaciones programadas que han llegado a su hora';

    public function handle()
    {
        $this->info('=== EJECUTOR DE PUBLICACIONES PROGRAMADAS ===');
        $this->info('Fecha y hora actual: ' . now()->format('Y-m-d H:i:s'));
        
        // obtiene las publicaciones programadas que deben ejecutarse
        $publicacionesPendientes = Publication::where('tipo_publicacion', 'programada')
            ->where('estado', 'pendiente')
            ->where('fecha_hora', '<=', now())
            ->orderBy('fecha_hora')
            ->get();

        if ($publicacionesPendientes->isEmpty()) {
            $this->info('No hay publicaciones programadas para ejecutar.');
            return 0;
        }

        $this->info("📅 Encontradas {$publicacionesPendientes->count()} publicaciones para ejecutar:");
        
        foreach ($publicacionesPendientes as $pub) {
            $this->line("   - ID {$pub->id}: {$pub->fecha_hora->format('H:i')} - " . Str::limit($pub->contenido, 50));
        }

        $this->newLine();
        
        $exitosas = 0;
        $fallidas = 0;

        foreach ($publicacionesPendientes as $publicacion) {
            $this->info("Procesando publicación ID: {$publicacion->id}");
            
            try {
                // Marcar como en proceso
                $publicacion->update(['estado' => 'en_proceso']);
                
                $resultados = [];
                $redesSeleccionadas = $publicacion->redes;
                
                foreach ($redesSeleccionadas as $red) {
                    try {
                        switch ($red) {
                            case 'mastodon':
                                $mastodonService = new MastodonService();
                                $resultado = $mastodonService->publicar(
                                    $publicacion->contenido, 
                                    'programada', 
                                    $publicacion->fecha_hora
                                );
                                $resultados[$red] = $resultado;
                                break;
                            
                            case 'linkedin':
                                $linkedInService = new LinkedInService();
                                $resultado = $linkedInService->publicar(
                                    $publicacion->contenido, 
                                    'programada', 
                                    $publicacion->fecha_hora
                                );
                                $resultados[$red] = $resultado;
                                break;
                        }
                    } catch (\Exception $e) {
                        $resultados[$red] = ['error' => $e->getMessage()];
                        Log::error("Error publicando en {$red}: " . $e->getMessage());
                    }
                }

                // Verificar si todas las publicaciones fueron exitosas
                $todasExitosas = collect($resultados)->every(function ($resultado) {
                    return !isset($resultado['error']);
                });

                // Actualizar estado y resultados
                $publicacion->update([
                    'estado' => $todasExitosas ? 'completada' : 'error',
                    'resultados' => $resultados
                ]);

                if ($todasExitosas) {
                    $exitosas++;
                    $this->info("✅ Publicación ID {$publicacion->id} ejecutada exitosamente");
                } else {
                    $fallidas++;
                    $this->warn("⚠️ Publicación ID {$publicacion->id} ejecutada con errores");
                }

            } catch (\Exception $e) {
                $fallidas++;
                $publicacion->update([
                    'estado' => 'error',
                    'resultados' => ['error' => $e->getMessage()]
                ]);
                
                Log::error("Error ejecutando publicación ID {$publicacion->id}: " . $e->getMessage());
                $this->error("❌ Error ejecutando publicación ID {$publicacion->id}: " . $e->getMessage());
            }
        }

        $this->newLine();
        $this->info("RESUMEN DE EJECUCIÓN:");
        $this->info("Exitosas: {$exitosas}");
        $this->info("Fallidas: {$fallidas}");
        $this->info("Total procesadas: " . ($exitosas + $fallidas));
        
        return 0;
    }
}