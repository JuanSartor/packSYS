<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Detalle de Unidad
            </h2>
            <a href="{{ route('unidades.index') }}" class="text-gray-600 hover:text-gray-900">
                Volver
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-semibold mb-4">Información de la Unidad</h3>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <p class="text-sm font-medium text-gray-500">ID</p>
                            <p class="mt-1 text-sm text-gray-900">{{ $unidad->id }}</p>
                        </div>

                        <div>
                            <p class="text-sm font-medium text-gray-500">Descripción</p>
                            <p class="mt-1 text-sm text-gray-900">{{ $unidad->descripcion }}</p>
                        </div>

                        <div>
                            <p class="text-sm font-medium text-gray-500">Fecha de Creación</p>
                            <p class="mt-1 text-sm text-gray-900">{{ $unidad->created_at->format('d/m/Y H:i') }}</p>
                        </div>

                        <div>
                            <p class="text-sm font-medium text-gray-500">Última Actualización</p>
                            <p class="mt-1 text-sm text-gray-900">{{ $unidad->updated_at->format('d/m/Y H:i') }}</p>
                        </div>
                    </div>

                    <div class="mt-6 flex space-x-3">
                        <a href="{{ route('unidades.edit', $unidad) }}" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700">
                            Editar
                        </a>
                        <form action="{{ route('unidades.destroy', $unidad) }}" method="POST" class="inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700" onclick="return confirmDelete(event, '¿Está seguro de eliminar esta unidad?')">
                                Eliminar
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            @if($unidad->products->count() > 0)
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold mb-4">Productos que usan esta unidad</h3>
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nombre</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tipo</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Stock Actual</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($unidad->products as $product)
                                        <tr>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                <a href="{{ route('products.show', $product) }}" class="text-blue-600 hover:text-blue-900">
                                                    {{ $product->name }}
                                                </a>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                {{ $product->productType ? $product->productType->nombre : 'N/A' }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                {{ formatNumber($product->stock_actual, 2) }}
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
