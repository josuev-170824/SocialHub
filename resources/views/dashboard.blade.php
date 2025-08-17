@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto">
    <!-- Header del Dashboard -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Dashboard</h1>
        <p class="text-gray-600">Bienvenido de vuelta, {{ Auth::user()->name }}!</p>
    </div>

    <!-- Tarjetas de Resumen -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <!-- Publicaciones Programadas -->
        <div class="bg-white rounded-xl shadow p-6">
            <div class="flex items-center">
                <div class="p-2 bg-blue-100 rounded-lg">
                    <x-heroicon-o-clock class="w-6 h-6 text-blue-600" />
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Programadas</p>
                    <p class="text-2xl font-semibold text-gray-900">0</p>
                </div>
            </div>
        </div>

        <!-- Publicaciones en Cola -->
        <div class="bg-white rounded-xl shadow p-6">
            <div class="flex items-center">
                <div class="p-2 bg-yellow-100 rounded-lg">
                    <x-heroicon-o-queue-list class="w-6 h-6 text-yellow-600" />
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">En Cola</p>
                    <p class="text-2xl font-semibold text-gray-900">0</p>
                </div>
            </div>
        </div>

        <!-- Redes Conectadas -->
        <div class="bg-white rounded-xl shadow p-6">
            <div class="flex items-center">
                <div class="p-2 bg-green-100 rounded-lg">
                    <x-heroicon-o-link class="w-6 h-6 text-green-600" />
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Redes</p>
                    <p class="text-2xl font-semibold text-gray-900">0</p>
                </div>
            </div>
        </div>

        <!-- Estado 2FA -->
        <div class="bg-white rounded-xl shadow p-6">
            <div class="flex items-center">
                <div class="p-2 {{ Auth::user()->google2fa_enabled ? 'bg-green-100' : 'bg-red-100' }} rounded-lg">
                    <x-heroicon-o-shield-check class="w-6 h-6 {{ Auth::user()->google2fa_enabled ? 'text-green-600' : 'text-red-600' }}" />
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">2FA</p>
                    <p class="text-2xl font-semibold {{ Auth::user()->google2fa_enabled ? 'text-green-600' : 'text-red-600' }}">
                        {{ Auth::user()->google2fa_enabled ? 'ON' : 'OFF' }}
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Acciones Rápidas -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
        <!-- Crear Nueva Publicación -->
        <div class="bg-white rounded-xl shadow p-6">
            <h3 class="text-lg font-semibold mb-4 flex items-center">
                <x-heroicon-o-plus-circle class="w-5 h-5 mr-2 text-indigo-600" />
                Crear Nueva Publicación
            </h3>
            <p class="text-gray-600 mb-4">Programa o publica inmediatamente en tus redes sociales conectadas.</p>
            <button class="px-6 py-3 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 font-medium">
                Crear Publicación
            </button>
        </div>

        <!-- Configuración Rápida -->
        <div class="bg-white rounded-xl shadow p-6">
            <h3 class="text-lg font-semibold mb-4 flex items-center">
                <x-heroicon-o-cog-6-tooth class="w-5 h-5 mr-2 text-gray-600" />
                Configuración Rápida
            </h3>
            <p class="text-gray-600 mb-4">Gestiona tu cuenta, redes sociales y configuraciones de seguridad.</p>
            <a href="{{ route('user.settings') }}" class="inline-block px-6 py-3 bg-gray-800 text-white rounded-lg hover:bg-gray-900 font-medium">
                Ir a Configuración
            </a>
        </div>
    </div>

    <!-- Actividad Reciente -->
    <div class="bg-white rounded-xl shadow p-6">
        <h3 class="text-lg font-semibold mb-4 flex items-center">
            <x-heroicon-o-clock class="w-5 h-5 mr-2 text-gray-600" />
            Actividad Reciente
        </h3>
        <div class="text-center py-8 text-gray-500">
            <x-heroicon-o-document-text class="w-12 h-12 mx-auto mb-4 text-gray-300" />
            <p class="text-lg">No hay actividad reciente</p>
            <p class="text-sm">Cuando comiences a publicar, verás tu historial aquí.</p>
        </div>
    </div>
</div>
@endsection