<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Publication extends Model
{
    use HasFactory;

    protected $table = 'publications';

    // Datos de la publicación
    protected $fillable = [
        'user_id',
        'contenido',
        'redes',
        'tipo_publicacion',
        'fecha_hora',
        'estado',
        'resultados'
    ];

    protected $casts = [
        'redes' => 'array',
        'fecha_hora' => 'datetime',
        'resultados' => 'array'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // Scope/filtro para las publicaciones programadas
    public function scopeProgramadas($query)
    {
        return $query->where('tipo_publicacion', 'programada')
                    ->where('fecha_hora', '>', now());
    }

    // Scope/filtro para las publicaciones pendientes
    public function scopePendientes($query)
    {
        return $query->where('estado', 'pendiente')
        ->where(function($q) {
            $q->where('tipo_publicacion', 'inmediata')
              ->orWhere(function($q2) {
                  $q2->where('tipo_publicacion', 'programada')
                      ->where('fecha_hora', '>', now());
              });
        });
    }

    // Scope/filtro para las publicaciones ejecutadas
    public function scopeEjecutadas($query)
    {
        return $query->where('estado', 'completada');
    }

    // Scope/filtro para las publicaciones en proceso
    public function scopePorEjecutar($query)
    {
        return $query->where('tipo_publicacion', 'programada')
                    ->where('fecha_hora', '>', now())
                    ->where('estado', 'pendiente');
    }

    // Scope/filtro para las publicaciones recientes
    public function scopeRecientes($query, $limit = 5)
    {
        return $query->orderBy('created_at', 'desc')->limit($limit);
    }

    // Método para verificar si se puede editar la publicación
    public function esEditable(): bool
    {
        // si la publicación es inmediata, no se puede editar
        if ($this->tipo_publicacion === 'inmediata') {
            return false;
        }
        
        // si la publicación es programada, se puede editar si la fecha y hora son mayores a la fecha y hora actual y el estado es pendiente
        if ($this->tipo_publicacion === 'programada') {
            return $this->fecha_hora > now() && $this->estado === 'pendiente';
        }
        
        return false;
    }

    // Método para verificar si se puede eliminar la publicación
    public function esEliminable(): bool
    {
        return $this->tipo_publicacion === 'inmediata' || ($this->tipo_publicacion === 'programada' && $this->fecha_hora > now() && $this->estado === 'pendiente');
    }

}