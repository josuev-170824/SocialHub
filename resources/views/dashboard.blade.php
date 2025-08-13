@extends('layouts.app')

@section('content')
<div class="mt-8 p-6 bg-gray-50 rounded-lg">
    <h3 class="text-lg font-semibold mb-4">Configuración de Seguridad</h3>
    
    @if(Auth::user()->google2fa_enabled)
        <div class="mb-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-green-700 font-medium">✅ 2FA Activado</p>
                    <p class="text-sm text-gray-600">Activado el {{ \Carbon\Carbon::parse(Auth::user()->google2fa_enabled_at)->format('d/m/Y H:i') }}</p>
                </div>
                <form method="POST" action="{{ route('2fa.disable') }}" class="inline">
                    @csrf
                    <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 text-sm">
                        Desactivar 2FA
                    </button>
                </form>
            </div>
        </div>
    @else
        <div class="mb-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-red-700 font-medium">❌ 2FA No Activado</p>
                    <p class="text-sm text-gray-600">Tu cuenta no tiene autenticación de dos factores</p>
                </div>
                <a href="{{ route('2fa.setup') }}" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 text-sm">
                    Activar 2FA
                </a>
            </div>
        </div>
    @endif
    
    <p class="text-xs text-gray-500">
        La autenticación de dos factores añade una capa extra de seguridad a tu cuenta.
    </p>
</div>
@endsection