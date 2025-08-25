<!-- Vista de publicaciones -->
@extends('layouts.app')

@section('content')
<!-- Contenedor principal -->
<div class="max-w-7xl mx-auto">
    <!-- Título y botón de nueva publicación -->
    <div class="mb-8 flex justify-between items-center">
        <!-- Título y descripción -->
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Mis Publicaciones</h1>
            <p class="text-gray-600">Gestiona todas tus publicaciones en redes sociales</p>
        </div>
        <!-- Botón de nueva publicación -->
        <a href="{{ route('publications.create') }}" class="inline-block px-6 py-3 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 font-medium">
            Nueva Publicación
        </a>
    </div>

    <!-- Lista de publicaciones -->
    <div class="bg-white rounded-xl shadow overflow-hidden">

        @if($publicaciones->count() > 0)

            <!-- Tabla de publicaciones -->
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">

                    <!-- Encabezados de la tabla -->
                    <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Contenido
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Redes
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Tipo
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Estado
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Fecha
                        </th>
                    </tr>
                </thead>

                    <!-- Cuerpo de la tabla -->
                    <tbody class="bg-white divide-y divide-gray-200">
                        <!-- Muestra las publicaciones -->
                        @foreach($publicaciones as $publicacion)
                        
                        <tr>
                            <!-- Contenido de la publicación -->
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">{{ Str::limit($publicacion->contenido, 50) }}</div>
                            </td>

                            <!-- Redes de la publicación -->
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex space-x-1">
                                    @if($publicacion->redes)
                                        @foreach($publicacion->redes as $red)
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                                {{ ucfirst($red) }}
                                            </span>
                                        @endforeach
                                    @else
                                        <span class="text-gray-400">N/A</span>
                                    @endif
                                </div>
                            </td>

                            <!-- Tipo de la publicación -->
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                    {{ $publicacion->tipo_publicacion === 'inmediata' ? 'bg-green-100 text-green-800' : 'bg-purple-100 text-purple-800' }}">
                                    {{ ucfirst($publicacion->tipo_publicacion) }}
                                </span>
                            </td>

                            <!-- Estado de la publicación -->
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                    {{ $publicacion->estado === 'completada' ? 'bg-green-100 text-green-800' : 
                                       ($publicacion->estado === 'pendiente' ? 'bg-yellow-100 text-yellow-800' : 'bg-gray-100 text-gray-800') }}">
                                    {{ ucfirst($publicacion->estado ?? 'pendiente') }}
                                </span>
                            </td>

                            <!-- Fecha de la publicación -->
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $publicacion->created_at->format('d/m/Y H:i') }}
                            </td>
                        </tr>

                        @endforeach
                    </tbody>
                </table>
            </div>

        @else

            <!-- Mensaje si no hay publicaciones -->
            <div class="text-center py-12">
                <div class="w-16 h-16 mx-auto mb-4 text-gray-300">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                </div>
                <h3 class="text-lg font-medium text-gray-900 mb-2">No hay publicaciones</h3>
                <p class="text-gray-500 mb-6">Comienza creando tu primera publicación</p>
                <a href="{{ route('publications.create') }}" class="inline-block px-6 py-3 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 font-medium">
                    Crear Primera Publicación
                </a>
            </div>

        @endif
    </div>
</div>
@endsection