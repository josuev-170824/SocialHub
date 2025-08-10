<!-- login.blade.php -->
@extends('layouts.app')

<!-- Contenido de la página -->
@section('content')
<div class="max-w-md mx-auto bg-white rounded-xl shadow p-8">
    <h1 class="text-2xl font-semibold mb-6">Iniciar sesión</h1>
    <!-- Manejo de errores -->
    @if ($errors->any())
        <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded mb-4">
            <ul class="list-disc list-inside">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
    <!-- Formulario de inicio de sesión -->
    <form method="POST" action="{{ route('login') }}" class="space-y-5">
        @csrf
        <div>
            <label class="block text-sm mb-2">Correo</label>
            <input type="email" name="email" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" required>
        </div>
        <div>
            <label class="block text-sm mb-2">Contraseña</label>
            <input type="password" name="password" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" required>
        </div>
        <div class="flex items-center justify-between">
            <label class="inline-flex items-center gap-2 text-sm">
                <input type="checkbox" name="remember" class="rounded border-gray-300">
                Recuérdame
            </label>
            <a href="#" class="text-sm text-indigo-600 hover:underline">¿Olvidaste tu contraseña?</a>
        </div>
        <button type="submit" class="w-full py-2.5 rounded-lg bg-indigo-600 text-white font-medium hover:bg-indigo-700">Entrar</button>
    </form>
    <!-- Enlace a la página de registro -->
    <p class="text-sm text-gray-600 mt-6">¿No tienes cuenta?
        <a class="text-indigo-600 hover:underline" href="/register">Crear cuenta</a>
    </p>
    <!-- Enlace a Google para autenticación -->
    <div class="mt-4">
  <div class="flex items-center gap-4 my-4">
    <div class="h-px bg-gray-200 flex-1"></div>
    <span class="text-xs text-gray-500">o</span>
    <div class="h-px bg-gray-200 flex-1"></div>
  </div>
  <a href="{{ route('google.redirect') }}" class="w-full inline-flex items-center justify-center gap-2 py-2.5 rounded-lg bg-red-600 text-white font-medium hover:bg-red-700">
    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 48 48"><path fill="#FFC107" d="M43.6 20.5H42V20H24v8h11.3C33.7 31.6 29.3 35 24 35c-6.6 0-12-5.4-12-12s5.4-12 12-12c3.1 0 5.9 1.2 8 3.1l5.7-5.7C34.6 5 29.6 3 24 3 12.3 3 3 12.3 3 24s9.3 21 21 21c10.5 0 20-7.6 20-21 0-1.3-.1-2.6-.4-3.5z"/><path fill="#FF3D00" d="M6.3 14.7l6.6 4.8C14.4 16 18.8 13 24 13c3.1 0 5.9 1.2 8 3.1l5.7-5.7C34.6 5 29.6 3 24 3 15.3 3 7.4 8.6 6.3 14.7z"/><path fill="#4CAF50" d="M24 45c5.2 0 10-1.8 13.6-5l-6.3-5.2C29.3 35 26.8 36 24 36c-5.2 0-9.6-3.3-11.3-7.9l-6.5 5C7.4 41.3 15.1 45 24 45z"/><path fill="#1976D2" d="M43.6 20.5H42V20H24v8h11.3c-1.2 3.6-4.6 7-11.3 7-6.6 0-12-5.4-12-12s5.4-12 12-12c3.1 0 5.9 1.2 8 3.1l5.7-5.7C34.6 5 29.6 3 24 3c-10.7 0-19.9 9.2-19.9 21S13.3 45 24 45c10.5 0 20-7.6 20-21 0-1.3-.1-2.6-.4-3.5z"/></svg>
    Continuar con Google
  </a>
</div>
    
</div>
@endsection