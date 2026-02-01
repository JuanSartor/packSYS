<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Detalle del Tipo de Producto
            </h2>
            <div class="space-x-2">
                <a href="{{ route('product-types.edit', $productType) }}" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                    Editar
                </a>
                <a href="{{ route('product-types.index') }}" class="text-gray-600 hover:text-gray-900">
                    Volver
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-semibold mb-4">Información del Tipo de Producto</h3>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <p class="text-sm font-medium text-gray-500">ID</p>
                            <p class="mt-1 text-sm text-gray-900">{{ $productType->id }}</p>
                        </div>

                        <div>
                            <p class="text-sm font-medium text-gray-500">Nombre</p>
                            <p class="mt-1 text-sm text-gray-900">{{ $productType->nombre }}</p>
                        </div>

                        @if($productType->descripcion)
                        <div class="col-span-2">
                            <p class="text-sm font-medium text-gray-500">Descripción</p>
                            <p class="mt-1 text-sm text-gray-900">{{ $productType->descripcion }}</p>
                        </div>
                        @endif

                        <div>
                            <p class="text-sm font-medium text-gray-500">Fecha de Creación</p>
                            <p class="mt-1 text-sm text-gray-900">{{ $productType->created_at->format('d/m/Y H:i') }}</p>
                        </div>

                        @if($productType->creator)
                        <div>
                            <p class="text-sm font-medium text-gray-500">Creado por</p>
                            <p class="mt-1 text-sm text-gray-900">{{ $productType->creator->name }}</p>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-semibold mb-4">Productos de este Tipo</h3>

                    @if($productType->products->count() > 0)
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Nombre
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Proveedor
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Stock Actual
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Acciones
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($productType->products as $product)
                                        <tr>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                {{ $product->name }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                {{ $product->proveedor ? $product->proveedor->nombre : '-' }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm {{ $product->stock_actual <= $product->stock_minimo ? 'text-red-600 font-bold' : 'text-gray-900' }}">
                                                {{ formatNumber($product->stock_actual, 2) }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                                <a href="{{ route('products.show', $product) }}" class="text-blue-600 hover:text-blue-900">
                                                    Ver
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="text-gray-500 text-sm">No hay productos asociados a este tipo.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
