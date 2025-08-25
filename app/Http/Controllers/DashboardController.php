<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\CuentaRedSocial;
use App\Models\Publication;


class DashboardController extends Controller
{
    // Vista del dashboard
    public function index()
    {
        $user = Auth::user();
        
        // Obtiene el número de redes conectadas
        $redesConectadas = $user->cuentasRedesSociales()
            ->where('activa', true)
            ->count();
            
        // Obtiene el número de publicaciones programadas
        $publicacionesProgramadas = 0;

        // Obtiene el número de publicaciones en cola
        $publicacionesEnCola = 0;

        // Obtiene las publicaciones recientes
        $actividadReciente = $user->publications()
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();


        return view('dashboard', compact(
            'redesConectadas',
            'publicacionesProgramadas', 
            'publicacionesEnCola',
            'actividadReciente'
        ));
    }
}