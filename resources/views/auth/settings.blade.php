@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto">
    <h1 class="text-3xl font-bold mb-8 flex items-center">
        <x-heroicon-o-cog-6-tooth class="w-8 h-8 mr-3 text-gray-600" />
        Configuración de Usuario
    </h1>
    
    <!-- Configuración de Seguridad -->
    <div class="bg-white rounded-xl shadow p-6 mb-6">
        <h3 class="text-xl font-semibold mb-4 flex items-center">
            <x-heroicon-o-shield-check class="w-5 h-5 mr-2 text-green-600" />
            Configuración de Seguridad
        </h3>
        
        @if(Auth::user()->google2fa_enabled)
            <div class="mb-4">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-green-700 font-medium flex items-center">
                            <x-heroicon-o-check-circle class="w-4 h-4 mr-2" />
                            2FA Activado
                        </p>
                        <p class="text-sm text-gray-600">Activado el {{ \Carbon\Carbon::parse(Auth::user()->google2fa_enabled_at)->format('d/m/Y H:i') }}</p>
                    </div>
                    <form method="POST" action="{{ route('2fa.disable') }}" class="inline">
                        @csrf
                        <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 text-sm flex items-center">
                            <x-heroicon-o-x-circle class="w-4 h-4 mr-2" />
                            Desactivar 2FA
                        </button>
                    </form>
                </div>
            </div>
        @else
            <div class="mb-4">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-red-700 font-medium flex items-center">
                            <x-heroicon-o-x-circle class="w-4 h-4 mr-2" />
                            2FA No Activado
                        </p>
                        <p class="text-sm text-gray-600">Tu cuenta no tiene autenticación de dos factores</p>
                    </div>
                    <a href="{{ route('2fa.setup') }}" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 text-sm flex items-center">
                        <x-heroicon-o-plus-circle class="w-4 h-4 mr-2" />
                        Activar 2FA
                    </a>
                </div>
            </div>
        @endif
        
        <p class="text-xs text-gray-500">
            La autenticación de dos factores añade una capa extra de seguridad a tu cuenta.
        </p>
    </div>

    <!-- Configuración de Redes Sociales -->
    <div class="bg-white rounded-xl shadow p-6 mb-6">
        <h3 class="text-xl font-semibold mb-4 flex items-center">
            <x-heroicon-o-link class="w-5 h-5 mr-2 text-blue-600" />
            Redes Sociales Conectadas
        </h3>
        <p class="text-gray-600 mb-4">Conecta tus cuentas de redes sociales para poder publicar desde aquí.</p>
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <!-- Twitter -->
            <div class="border border-gray-200 rounded-lg p-4 text-center">
                <div class="text-blue-400 text-3xl mb-2">
                    <x-heroicon-o-chat-bubble-left-right class="w-12 h-12 mx-auto text-blue-400" />
                </div>
                <h4 class="font-medium mb-2">Twitter</h4>
                <p class="text-sm text-gray-500 mb-3">No conectado</p>
                <button class="px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600 text-sm flex items-center mx-auto">
                    <x-heroicon-o-plus class="w-4 h-4 mr-2" />
                    Conectar Twitter
                </button>
            </div>

            <!-- Facebook -->
            <div class="border border-gray-200 rounded-lg p-4 text-center">
                <div class="text-blue-600 text-3xl mb-2">
                    <x-heroicon-o-rectangle-stack class="w-12 h-12 mx-auto text-blue-600" />
                </div>
                <h4 class="font-medium mb-2">Facebook</h4>
                <p class="text-sm text-gray-500 mb-3">No conectado</p>
                <button class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 text-sm flex items-center mx-auto">
                    <x-heroicon-o-plus class="w-4 h-4 mr-2" />
                    Conectar Facebook
                </button>
            </div>

            <!-- LinkedIn -->
            <div class="border border-gray-200 rounded-lg p-4 text-center">
                <div class="text-blue-700 text-3xl mb-2">
                    <x-heroicon-o-briefcase class="w-12 h-12 mx-auto text-blue-700" />
                </div>
                <h4 class="font-medium mb-2">LinkedIn</h4>
                <p class="text-sm text-gray-500 mb-3">No conectado</p>
                <button class="px-4 py-2 bg-blue-700 text-white rounded-lg hover:bg-blue-800 text-sm flex items-center mx-auto">
                    <x-heroicon-o-plus class="w-4 h-4 mr-2" />
                    Conectar LinkedIn
                </button>
            </div>
        </div>
    </div>

    <!-- Información del Usuario -->
    <div class="bg-white rounded-xl shadow p-6">
        <h3 class="text-xl font-semibold mb-4 flex items-center">
            <x-heroicon-o-user-circle class="w-5 h-5 mr-2 text-gray-600" />
            Información de la Cuenta
        </h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Nombre</label>
                <p class="text-gray-900">{{ Auth::user()->name }}</p>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                <p class="text-gray-900">{{ Auth::user()->email }}</p>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Miembro desde</label>
                <p class="text-gray-900">{{ \Carbon\Carbon::parse(Auth::user()->created_at)->format('d/m/Y') }}</p>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Última actividad</label>
                <p class="text-gray-900">{{ \Carbon\Carbon::parse(Auth::user()->updated_at)->format('d/m/Y H:i') }}</p>
            </div>
        </div>
    </div>
</div>
@endsection