@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto">
    <!-- Header -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Calendario de Publicaciones</h1>
        <p class="text-gray-600">Visualiza y gestiona todas tus publicaciones programadas</p>
    </div>

    <!-- Filtros -->
    <div class="bg-white rounded-xl shadow p-4 mb-6">
        <div class="flex items-center space-x-4">
            <label class="flex items-center">
                <input type="checkbox" id="mostrarPasadas" class="mr-2" checked>
                <span class="text-sm text-gray-700">Mostrar publicaciones pasadas</span>
            </label>
            <span class="text-sm text-gray-500">
                Total: <span id="totalPublicaciones">0</span> | 
                Pendientes: <span id="totalPendientes">0</span> | 
                Completadas: <span id="totalCompletadas">0</span>
            </span>
        </div>
    </div>

    <!-- Calendario mensual -->
    <div class="bg-white rounded-xl shadow overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <h3 class="text-lg font-semibold">Calendario Mensual</h3>
                <div class="flex items-center space-x-4">
                    <button id="mesAnterior" class="p-2 hover:bg-gray-100 rounded-lg">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                        </svg>
                    </button>
                    <span id="mesActual" class="text-lg font-medium">Agosto 2025</span>
                    <button id="mesSiguiente" class="p-2 hover:bg-gray-100 rounded-lg">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                        </svg>
                    </button>
                </div>
            </div>
        </div>

        <!-- Calendario -->
        <div class="p-6">
            <!-- Días de la semana -->
            <div class="grid grid-cols-7 gap-1 mb-2">
                <div class="text-center font-medium text-gray-500 text-sm p-2">Dom</div>
                <div class="text-center font-medium text-gray-500 text-sm p-2">Lun</div>
                <div class="text-center font-medium text-gray-500 text-sm p-2">Mar</div>
                <div class="text-center font-medium text-gray-500 text-sm p-2">Mié</div>
                <div class="text-center font-medium text-gray-500 text-sm p-2">Jue</div>
                <div class="text-center font-medium text-gray-500 text-sm p-2">Vie</div>
                <div class="text-center font-medium text-gray-500 text-sm p-2">Sáb</div>
            </div>

            <!-- Días del mes -->
            <div id="calendarioDias" class="grid grid-cols-7 gap-1">
                <!-- Se llena dinámicamente con JavaScript -->
            </div>
        </div>

        <!-- Información adicional -->
        <div class="px-6 py-4 bg-gray-50 border-t border-gray-200">
            <div class="flex items-center justify-between text-sm text-gray-600">
                <div>
                    <span class="font-medium">Próxima publicación:</span> 
                    <span id="proximaPublicacion">Ninguna</span>
                </div>
                <div>
                    <span class="font-medium">Última publicación:</span> 
                    <span id="ultimaPublicacion">Ninguna</span>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    let fechaActual = new Date();
    let publicaciones = @json($publicaciones);
    
    // Inicializar calendario
    mostrarCalendario(fechaActual);
    actualizarContadores();
    
    // Eventos de navegación
    document.getElementById('mesAnterior').addEventListener('click', () => {
        fechaActual.setMonth(fechaActual.getMonth() - 1);
        mostrarCalendario(fechaActual);
    });
    
    document.getElementById('mesSiguiente').addEventListener('click', () => {
        fechaActual.setMonth(fechaActual.getMonth() + 1);
        mostrarCalendario(fechaActual);
    });
    
    // Filtro de publicaciones pasadas
    document.getElementById('mostrarPasadas').addEventListener('change', function() {
        mostrarCalendario(fechaActual);
    });
    
    function mostrarCalendario(fecha) {
        const mesActual = document.getElementById('mesActual');
        mesActual.textContent = fecha.toLocaleDateString('es-ES', { month: 'long', year: 'numeric' });
        
        const calendario = document.getElementById('calendarioDias');
        calendario.innerHTML = '';
        
        const primerDia = new Date(fecha.getFullYear(), fecha.getMonth(), 1);
        const ultimoDia = new Date(fecha.getFullYear(), fecha.getMonth() + 1, 0);
        const primerDiaSemana = primerDia.getDay();
        
        // Días vacíos al inicio
        for (let i = 0; i < primerDiaSemana; i++) {
            calendario.appendChild(crearCeldaVacia());
        }
        
        // Días del mes
        for (let dia = 1; dia <= ultimoDia.getDate(); dia++) {
            const fechaDia = new Date(fecha.getFullYear(), fecha.getMonth(), dia);
            calendario.appendChild(crearCeldaDia(fechaDia, dia));
        }
    }
    
    function crearCeldaDia(fecha, dia) {
        const celda = document.createElement('div');
        celda.className = 'min-h-[100px] border border-gray-200 p-2 bg-white hover:bg-gray-50';
        
        const publicacionesDelDia = publicaciones.filter(pub => {
            const fechaPub = new Date(pub.fecha_hora);
            return fechaPub.getDate() === dia && 
                   fechaPub.getMonth() === fecha.getMonth() && 
                   fechaPub.getFullYear() === fecha.getFullYear();
        });
        
        let contenido = `<div class="text-sm font-medium text-gray-900 mb-2">${dia}</div>`;
        
        if (publicacionesDelDia.length > 0) {
            publicacionesDelDia.forEach(pub => {
                const fechaPub = new Date(pub.fecha_hora);
                const esPasada = fechaPub < new Date();
                const estadoClase = esPasada ? 'bg-gray-200 text-gray-600' : 'bg-blue-100 text-blue-800';
                const estadoTexto = esPasada ? 'Pasada' : 'Pendiente';
                
                contenido += `
                    <div class="mb-2 p-2 rounded text-xs ${estadoClase}">
                        <div class="font-medium">${fechaPub.toLocaleTimeString('es-ES', { hour: '2-digit', minute: '2-digit' })}</div>
                        <div class="truncate">${pub.contenido.substring(0, 25)}${pub.contenido.length > 25 ? '...' : ''}</div>
                        <div class="text-xs opacity-75">${estadoTexto}</div>
                        ${!esPasada ? `
                            <div class="flex space-x-1 mt-1">
                                <button onclick="editarPublicacion(${pub.id})" class="text-xs text-indigo-600 hover:text-indigo-900">Editar</button>
                                <button onclick="eliminarPublicacion(${pub.id})" class="text-xs text-red-600 hover:text-red-900">Eliminar</button>
                            </div>
                        ` : ''}
                    </div>
                `;
            });
        }
        
        celda.innerHTML = contenido;
        return celda;
    }
    
    function crearCeldaVacia() {
        const celda = document.createElement('div');
        celda.className = 'min-h-[100px] border border-gray-200 p-2 bg-gray-50';
        return celda;
    }
    
    function actualizarContadores() {
        const total = publicaciones.length;
        const pendientes = publicaciones.filter(p => new Date(p.fecha_hora) > new Date()).length;
        const completadas = total - pendientes;
        
        document.getElementById('totalPublicaciones').textContent = total;
        document.getElementById('totalPendientes').textContent = pendientes;
        document.getElementById('totalCompletadas').textContent = completadas;
        
        // Próxima y última publicación
        const futuras = publicaciones.filter(p => new Date(p.fecha_hora) > new Date()).sort((a, b) => new Date(a.fecha_hora) - new Date(b.fecha_hora));
        const pasadas = publicaciones.filter(p => new Date(p.fecha_hora) <= new Date()).sort((a, b) => new Date(b.fecha_hora) - new Date(a.fecha_hora));
        
        if (futuras.length > 0) {
            document.getElementById('proximaPublicacion').textContent = new Date(futuras[0].fecha_hora).toLocaleDateString('es-ES');
        }
        if (pasadas.length > 0) {
            document.getElementById('ultimaPublicacion').textContent = new Date(pasadas[0].fecha_hora).toLocaleDateString('es-ES');
        }
    }
});

function editarPublicacion(id) {
    window.location.href = `/publications/${id}/edit`;
}

function eliminarPublicacion(id) {
    if (confirm('¿Estás seguro de que quieres eliminar esta publicación?')) {
        fetch(`/publications/${id}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        }).then(() => {
            window.location.reload();
        });
    }
}
</script>
@endsection