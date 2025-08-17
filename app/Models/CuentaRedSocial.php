<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CuentaRedSocial extends Model
{
    protected $table = 'cuentas_redes_sociales';
    
    // Columnas de la tabla
    protected $fillable = [
        'user_id', // ID del usuario
        'plataforma', // 'twitter', 'facebook', 'linkedin'
        'id_plataforma', // ID del usuario en la red social
        'nombre_usuario', // @usuario, nombre de página, etc.
        'token_acceso', // Token de acceso OAuth (para autenticarse en la API)
        'token_refresco', // Token de refresco para renovar el token de acceso
        'token_expiracion', // Cuándo expira el token
        'datos_adicionales', // Datos extra de la API
        'activa' // Estado de la cuenta
    ];

    protected $casts = [
        'datos_adicionales' => 'array',
        'token_expiracion' => 'datetime',
        'activa' => 'boolean'
    ];

    // Relación con el usuario
    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // Métodos para verificar el tipo de red social (Twitter, Facebook, LinkedIn)
    public function esTwitter(): bool
    {
        return $this->plataforma === 'twitter';
    }

    public function esFacebook(): bool
    {
        return $this->plataforma === 'facebook';
    }

    public function esLinkedIn(): bool
    {
        return $this->plataforma === 'linkedin';
    }

    // Método para verificar si el token ha expirado
    public function tokenExpirado(): bool
    {
        // Si no hay token de expiración, la cuenta no ha expirado
        if (!$this->token_expiracion) {
            return false;
        }
        // Si el token de expiración es anterior a la fecha y hora actual, la cuenta ya expiró
        return $this->token_expiracion->isPast();
    }
}