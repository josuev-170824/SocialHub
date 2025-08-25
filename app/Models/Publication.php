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
        return $query->where('estado', 'pendiente');
    }

    // Scope/filtro para las publicaciones recientes
    public function scopeRecientes($query, $limit = 5)
    {
        return $query->orderBy('created_at', 'desc')->limit($limit);
    }
}