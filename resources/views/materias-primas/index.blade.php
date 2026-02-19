<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Materias Primas
            </h2>
            <a href="{{ route('materias-primas.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                Nueva Materia Prima
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if(session('success'))
                <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
                    {{ session('success') }}
                </div>
            @endif

            {{-- Filtros --}}
            <div class="mb-4 bg-white overflow-hidden shadow-sm sm:rounded-lg p-4">
                <form method="GET" action="{{ route('materias-primas.index') }}" class="flex flex-wrap gap-3 items-end">
                    <div class="flex-1 min-w-[200px]">
                        <label class="text-sm font-bold text-gray-600">Buscar</label>
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Buscar por nombre, descripcion..."
                            class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 text-sm">
                    </div>
                    <button type="submit" class="bg-gray-600 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded text-sm">
                        Filtrar
                    </button>
                    <a href="{{ route('materias-primas.index') }}" class="text-gray-500 hover:text-gray-700 text-sm py-2">
                        Limpiar
                    </a>
                </form>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nombre</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Unidad</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Stock Actual</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Alerta</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($materiasPrimas as $mp)
                                <tr class="{{ $mp->stock_actual <= $mp->alerta_minima && $mp->alerta_minima > 0 ? 'bg-red-50' : '' }}">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $mp->id }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ $mp->nombre }}
                                        @if($mp->descripcion)
                                            <span class="text-xs text-gray-400 block">{{ Str::limit($mp->descripcion, 40) }}</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $mp->unidad_consumo }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm {{ $mp->stock_actual <= $mp->alerta_minima && $mp->alerta_minima > 0 ? 'text-red-600 font-bold' : 'text-gray-900' }}">
                                        {{ formatNumber($mp->stock_actual, 2) }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ formatNumber($mp->alerta_minima, 2) }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <a href="{{ route('materias-primas.show', $mp) }}" class="text-green-600 hover:text-green-900 mr-3">Ver</a>
                                        <a href="{{ route('materias-primas.edit', $mp) }}" class="text-blue-600 hover:text-blue-900 mr-3">Editar</a>
                                        <form action="{{ route('materias-primas.destroy', $mp) }}" method="POST" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:text-red-900" onclick="return confirmDelete(event, '¿Está seguro de eliminar esta materia prima?')">
                                                Eliminar
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-4 text-center text-sm text-gray-500">
                                        No hay materias primas registradas
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>

                    <div class="mt-4">
                        {{ $materiasPrimas->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
