@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto">
    <h1 class="text-3xl font-bold mb-8 flex items-center">
        <x-heroicon-o-pencil class="w-8 h-8 mr-3 text-indigo-600" />
        Editar Publicación Programada
    </h1>

    <!-- Información de la publicación -->
    <div class="bg-white rounded-xl shadow p-6 mb-6">
        <h3 class="text-lg font-semibold mb-4">Información de la Publicación</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
            <div>
                <span class="font-medium text-gray-700">Estado:</span>
                <span class="ml-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                    Pendiente
                </span>
            </div>
            <div>
                <span class="font-medium text-gray-700">Fecha Programada:</span>
                <span class="ml-2 text-gray-600">{{ $publication->fecha_hora->format('d/m/Y H:i') }}</span>
            </div>
        </div>
    </div>

    <!-- Formulario de edición -->
    <form method="POST" action="{{ route('publications.update', $publication) }}" class="bg-white rounded-xl shadow p-6">
        @csrf
        @method('PUT')
        
        <!-- Redes Sociales -->
        <div class="mb-6">
            <label class="block text-sm font-medium text-gray-700 mb-2">
                Seleccionar Redes Sociales
            </label>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                @if(isset($cuentaLinkedIn) && $cuentaLinkedIn)
                <label class="flex items-center p-3 border border-gray-200 rounded-lg cursor-pointer hover:bg-gray-50">
                    <input type="checkbox" name="redes[]" value="linkedin" 
                           {{ in_array('linkedin', $publication->redes) ? 'checked' : '' }}
                           class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                    <div class="ml-3">
                        <div class="flex items-center">
                            <x-heroicon-o-briefcase class="w-5 h-5 text-blue-700 mr-2" />
                            <span class="font-medium">LinkedIn</span>
                        </div>
                        <p class="text-sm text-gray-500">{{ $cuentaLinkedIn->nombre_usuario }}</p>
                    </div>
                </label>
                @endif

                @if(isset($cuentaMastodon) && $cuentaMastodon)
                <label class="flex items-center p-3 border border-gray-200 rounded-lg cursor-pointer hover:bg-gray-50">
                    <input type="checkbox" name="redes[]" value="mastodon" 
                           {{ in_array('mastodon', $publication->redes) ? 'checked' : '' }}
                           class="w-4 h-4 text-purple-600 border-gray-300 rounded focus:ring-purple-500">
                    <div class="ml-3">
                        <div class="flex items-center">
                            <x-heroicon-o-chat-bubble-left-right class="w-5 h-5 text-purple-600 mr-2" />
                            <span class="font-medium">Mastodon</span>
                        </div>
                        <p class="text-sm text-gray-500">{{ $cuentaMastodon->nombre_usuario }}</p>
                    </div>
                </label>
                @endif
            </div>
            
            @if(!isset($cuentaLinkedIn) && !isset($cuentaMastodon))
            <div class="text-center py-4 text-gray-500">
                <p class="text-sm">No tienes redes sociales conectadas</p>
                <a href="{{ route('user.settings') }}" class="text-indigo-600 hover:text-indigo-800 text-sm">
                    Conectar Redes Sociales
                </a>
            </div>
            @endif
        </div>
        
        <!-- Contenido del Post -->
        <div class="mb-6">
            <label for="contenido" class="block text-sm font-medium text-gray-700 mb-2">
                Contenido de la Publicación
            </label>
            <textarea 
                id="contenido" 
                name="contenido" 
                rows="6" 
                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                placeholder="¿Qué quieres compartir hoy?"
                maxlength="500"
                required
            >{{ $publication->contenido }}</textarea>
            <div class="flex justify-between items-center mt-2">
                <p class="text-sm text-gray-500">Máximo 500 caracteres</p>
                <span id="contador" class="text-sm text-gray-500">{{ strlen($publication->contenido) }}/500</span>
            </div>
        </div>

        <!-- Fecha y Hora Programada -->
        <div class="mb-6">
            <label class="block text-sm font-medium text-gray-700 mb-2">
                Fecha y Hora de Publicación
            </label>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label for="fecha" class="block text-sm text-gray-600 mb-1">Fecha</label>
                    <input 
                        type="date" 
                        id="fecha" 
                        name="fecha" 
                        value="{{ $publication->fecha_hora->format('Y-m-d') }}"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                        min="{{ date('Y-m-d') }}"
                        required
                    >
                </div>
                <div>
                    <label for="hora" class="block text-sm text-gray-600 mb-1">Hora</label>
                    <input 
                        type="time" 
                        id="hora" 
                        name="hora" 
                        value="{{ $publication->fecha_hora->format('H:i') }}"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                        required
                    >
                </div>
            </div>
            <p class="text-sm text-gray-500 mt-2">Solo puedes editar publicaciones pendientes</p>
        </div>

        <!-- Botones de Acción -->
        <div class="flex justify-end space-x-4">
            <a href="{{ route('schedules.index') }}" class="px-6 py-3 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50">
                Cancelar
            </a>
            <button type="submit" class="px-6 py-3 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">
                Actualizar Publicación
            </button>
        </div>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const contador = document.getElementById('contador');
    const contenido = document.getElementById('contenido');
    const fecha = document.getElementById('fecha');
    const hora = document.getElementById('hora');

    // Contador de caracteres
    contenido.addEventListener('input', function() {
        const longitud = this.value.length;
        contador.textContent = `${longitud}/500`;
        
        if (longitud > 450) {
            contador.classList.add('text-red-500');
        } else {
            contador.classList.remove('text-red-500');
        }
    });

    // Validación de fecha y hora
    [fecha, hora].forEach(input => {
        input.addEventListener('change', function() {
            if (fecha.value && hora.value) {
                const fechaSeleccionada = new Date(fecha.value + ' ' + hora.value);
                const ahora = new Date();
                const unMinutoDespues = new Date(ahora.getTime() + 60000);
                
                if (fechaSeleccionada <= ahora) {
                    alert('La fecha y hora deben ser al menos 1 minuto en el futuro');
                    fecha.value = '';
                    hora.value = '';
                }
            }
        });
    });
});
</script>
@endsection