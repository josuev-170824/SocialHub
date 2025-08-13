@extends('layouts.app')

@section('content')
<div class="max-w-md mx-auto bg-white rounded-xl shadow p-8">
    <h1 class="text-2xl font-semibold mb-6">Configurar Autenticación de Dos Factores</h1>
    <p class="text-sm text-gray-600 mb-4">
        Configura la autenticación de dos factores para mejorar la seguridad de su cuenta.
    </p>

    <div class="mb-6">
        <h2 class="text-lg font-medium mb-3">Paso 1: Escanear Código QR</h2>
        <p class="text-sm text-gray-600 mb-4">
            Escanea este código QR con Google Authenticator o cualquier app similar:
        </p>
        
        <div class="bg-gray-100 p-4 rounded-lg text-center">
            <div class="mb-3">
                {!! $qrCodeSvg !!}
            </div>
            <p class="text-xs text-gray-500">O ingresa manualmente: <code class="bg-gray-200 px-2 py-1 rounded">{{ $secret }}</code></p>
        </div>
    </div>


    <div class="mb-6">
        <h2 class="text-lg font-medium mb-3">Paso 2: Verificar Código</h2>
        <p class="text-sm text-gray-600 mb-4">
            Ingresa el código de 6 dígitos que aparece en tu app:
        </p>
        
        <form method="POST" action="{{ route('2fa.enable') }}" class="space-y-4">
            @csrf
            <div>
                <input type="text" name="code" placeholder="000000" 
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-center text-lg tracking-widest"
                       maxlength="6" required>
            </div>
            
            @if ($errors->any())
                <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded">
                    @foreach ($errors->all() as $error)
                        <p>{{ $error }}</p>
                    @endforeach
                </div>
            @endif
            
            <button type="submit" class="w-full py-2.5 rounded-lg bg-indigo-600 text-white font-medium hover:bg-indigo-700">
                Activar 2FA
            </button>
        </form>
    </div>
    
    <div class="text-center">
        <a href="{{ route('dashboard') }}" class="text-sm text-indigo-600 hover:underline">
            Configurar más tarde
        </a>
    </div>
</div>
@endsection