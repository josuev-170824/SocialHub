@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto bg-white rounded-xl shadow p-8">
    <h1 class="text-3xl font-bold mb-6">Dashboard</h1>
    
    <div class="bg-green-50 border border-green-200 rounded-lg p-4 mb-6">
        <p class="text-green-800">¡Bienvenido, {{ Auth::user()->name }}!</p>
        <p class="text-green-700 text-sm">Email: {{ Auth::user()->email }}</p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div class="bg-blue-50 border border-blue-200 rounded-lg p-6">
            <h3 class="text-lg font-semibold text-blue-800 mb-2">Redes Sociales</h3>
            <p class="text-blue-700">Conecta tus cuentas de redes sociales</p>
        </div>
        
        <div class="bg-purple-50 border border-purple-200 rounded-lg p-6">
            <h3 class="text-lg font-semibold text-purple-800 mb-2">Publicaciones</h3>
            <p class="text-purple-700">Gestiona tu contenido</p>
        </div>
    </div>

    <form method="POST" action="{{ route('logout') }}" class="mt-8">
        @csrf
        <button type="submit" class="px-6 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700">
            Cerrar sesión
        </button>
    </form>
</div>
@endsection