@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto">
    <h1 class="text-3xl font-bold mb-8 flex items-center">
        <x-heroicon-o-plus-circle class="w-8 h-8 mr-3 text-indigo-600" />
        Crear Nueva Publicación
    </h1>

    <!-- Formulario de Publicación -->
    @if(isset($cuentaLinkedIn) || isset($cuentaMastodon))
    <form method="POST" action="{{ route('publications.store') }}" class="bg-white rounded-xl shadow p-6">
        @csrf
        
        <!-- Selección de Redes Sociales -->
        <div class="mb-6">
            <h3 class="text-xl font-semibold mb-4 flex items-center">
                <x-heroicon-o-link class="w-5 h-5 mr-2 text-blue-600" />
                Seleccionar Redes Sociales
            </h3>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                @if(isset($cuentaLinkedIn) && $cuentaLinkedIn)
                <label class="flex items-center p-4 border border-gray-200 rounded-lg cursor-pointer hover:bg-gray-50">
                    <input type="checkbox" name="redes[]" value="linkedin" class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
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
                <label class="flex items-center p-4 border border-gray-200 rounded-lg cursor-pointer hover:bg-gray-50">
                    <input type="checkbox" name="redes[]" value="mastodon" class="w-4 h-4 text-purple-600 border-gray-300 rounded focus:ring-purple-500">
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
            ></textarea>
            <div class="flex justify-between items-center mt-2">
                <p class="text-sm text-gray-500">Máximo 500 caracteres</p>
                <span id="contador" class="text-sm text-gray-500">0/500</span>
            </div>
        </div>

        <!-- Opciones de Publicación -->
        <div class="mb-6">
            <label for="tipo_publicacion" class="block text-sm font-medium text-gray-700 mb-2">
                Tipo de Publicación
            </label>
            <select id="tipo_publicacion" name="tipo_publicacion" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                <option value="inmediata">Publicación Inmediata</option>
                <option value="programada">Publicación Programada</option>
            </select>
        </div>

        <!-- Fecha y Hora Programada (oculto por defecto) -->
        <div id="fecha_programada" class="mb-6 hidden">
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
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                        min="{{ date('Y-m-d') }}"
                    >
                </div>
                <div>
                    <label for="hora" class="block text-sm text-gray-600 mb-1">Hora</label>
                    <input 
                        type="time" 
                        id="hora" 
                        name="hora" 
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                    >
                </div>
            </div>
            <p class="text-sm text-gray-500 mt-2">Selecciona fecha y hora para la publicación programada</p>
        </div>

        <!-- Botones de Acción -->
        <div class="flex justify-end space-x-4">
            <a href="{{ route('dashboard') }}" class="px-6 py-3 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50">
                Cancelar
            </a>
            <button type="submit" class="px-6 py-3 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">
                Publicar
            </button>
        </div>
    </form>
    @else
    <!-- Mensaje cuando no hay redes conectadas -->
    <div class="bg-white rounded-xl shadow p-6 text-center py-8 text-gray-500">
        <x-heroicon-o-link class="w-12 h-12 mx-auto mb-4 text-gray-300" />
        <p class="text-lg">No tienes redes sociales conectadas</p>
        <p class="text-sm mb-4">Conecta al menos una red social para poder publicar.</p>
        <a href="{{ route('user.settings') }}" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
            Conectar Redes Sociales
        </a>
    </div>
    @endif
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const contador = document.getElementById('contador');
    const contenido = document.getElementById('contenido');
    const tipoPublicacion = document.getElementById('tipo_publicacion');
    const fechaProgramada = document.getElementById('fecha_programada');
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

    // Mostrar/ocultar fecha programada
    tipoPublicacion.addEventListener('change', function() {
        if (this.value === 'programada') {
            fechaProgramada.classList.remove('hidden');
            // Hacer obligatorios los campos de fecha y hora
            fecha.required = true;
            hora.required = true;
        } else {
            fechaProgramada.classList.add('hidden');
            // Quitar obligatoriedad
            fecha.required = false;
            hora.required = false;
            // Limpiar valores
            fecha.value = '';
            hora.value = '';
        }
    });

    // Validación de fecha y hora
    if (fecha && hora) {
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
    }
});
</script>
@endsection