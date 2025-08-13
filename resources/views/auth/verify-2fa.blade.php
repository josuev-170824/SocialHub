@extends('layouts.app')

@section('content')
<div class="max-w-md mx-auto bg-white rounded-xl shadow p-8">
    <h1 class="text-2xl font-semibold mb-6">Verificar Autenticación de Dos Factores</h1>
    
    <div class="mb-6">
        <p class="text-sm text-gray-600 mb-4">
            Ingresa el código de 6 dígitos de tu app Google Authenticator:
        </p>
        
        <form method="POST" action="{{ route('2fa.verify.post') }}" class="space-y-4">
            @csrf
            <div>
                <input type="text" name="code" placeholder="000000" 
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-center text-lg tracking-widest"
                       maxlength="6" required autofocus>
            </div>
            
            @if ($errors->any())
                <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded">
                    @foreach ($errors->all() as $error)
                        <p>{{ $error }}</p>
                    @endforeach
                </div>
            @endif
            
            <button type="submit" class="w-full py-2.5 rounded-lg bg-indigo-600 text-white font-medium hover:bg-indigo-700">
                Verificar y Continuar
            </button>
        </form>
    </div>
    
    <div class="text-center">
        <p class="text-sm text-gray-500">
            ¿Problemas con tu 2FA? 
            <a href="{{ route('dashboard') }}" class="text-indigo-600 hover:underline">Contacta soporte</a>
        </p>
    </div>
</div>
@endsection