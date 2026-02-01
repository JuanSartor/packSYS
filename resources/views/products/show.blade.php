<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Detalle del Producto
            </h2>
            <a href="{{ route('products.index') }}" class="text-gray-600 hover:text-gray-900">
                Volver
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-semibold mb-4">Información del Producto</h3>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <p class="text-sm font-medium text-gray-500">ID</p>
                            <p class="mt-1 text-sm text-gray-900">{{ $product->id }}</p>
                        </div>

                        <div>
                            <p class="text-sm font-medium text-gray-500">Nombre</p>
                            <p class="mt-1 text-sm text-gray-900">{{ $product->name }}</p>
                        </div>

                        @if($product->descripcion)
                        <div class="col-span-2">
                            <p class="text-sm font-medium text-gray-500">Descripción</p>
                            <p class="mt-1 text-sm text-gray-900">{{ $product->descripcion }}</p>
                        </div>
                        @endif

                        <div>
                            <p class="text-sm font-medium text-gray-500">Tipo</p>
                            <p class="mt-1">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                    @if($product->type === 'bolsa_papel') bg-blue-100 text-blue-800
                                    @elseif($product->type === 'friselina') bg-green-100 text-green-800
                                    @elseif($product->type === 'caja') bg-yellow-100 text-yellow-800
                                    @else bg-gray-100 text-gray-800
                                    @endif">
                                    {{ ucfirst(str_replace('_', ' ', $product->type)) }}
                                </span>
                            </p>
                        </div>

                        <div>
                            <p class="text-sm font-medium text-gray-500">Unidad</p>
                            <p class="mt-1 text-sm text-gray-900">{{ ucfirst($product->unidad) }}</p>
                        </div>

                        <div>
                            <p class="text-sm font-medium text-gray-500">Stock Actual</p>
                            <p class="mt-1 text-sm {{ $product->stock_actual <= $product->stock_minimo ? 'text-red-600 font-bold' : 'text-gray-900' }}">
                                {{ formatNumber($product->stock_actual, 2) }}
                                @if($product->stock_actual <= $product->stock_minimo)
                                    <span class="text-xs">(⚠ Bajo stock)</span>
                                @endif
                            </p>
                        </div>

                        <div>
                            <p class="text-sm font-medium text-gray-500">Stock Mínimo</p>
                            <p class="mt-1 text-sm text-gray-900">{{ formatNumber($product->stock_minimo, 2) }}</p>
                        </div>

                        <div>
                            <p class="text-sm font-medium text-gray-500">Usa Bobina</p>
                            <p class="mt-1 text-sm">
                                @if($product->usa_bobina)
                                    <span class="text-green-600 font-semibold">Sí</span>
                                @else
                                    <span class="text-gray-400">No</span>
                                @endif
                            </p>
                        </div>

                        @if($product->usa_bobina && ($product->ancho || $product->largo || $product->fuelle))
                        <div class="col-span-2">
                            <p class="text-sm font-medium text-gray-500 mb-2">Dimensiones</p>
                            <div class="flex gap-4 text-sm text-gray-900">
                                @if($product->ancho)
                                    <div>
                                        <span class="font-medium">Ancho:</span> {{ formatNumber($product->ancho, 2) }} cm
                                    </div>
                                @endif
                                @if($product->largo)
                                    <div>
                                        <span class="font-medium">Largo:</span> {{ formatNumber($product->largo, 2) }} cm
                                    </div>
                                @endif
                                @if($product->fuelle)
                                    <div>
                                        <span class="font-medium">Fuelle:</span> {{ formatNumber($product->fuelle, 2) }} cm
                                    </div>
                                @endif
                            </div>
                        </div>
                        @endif

                        <div>
                            <p class="text-sm font-medium text-gray-500">Precio Actual</p>
                            <p class="mt-1 text-sm text-gray-900">
                                @if($product->currentPrice())
                                    {{ formatCurrency($product->currentPrice()->precio, 2) }}
                                @else
                                    <span class="text-gray-400">Sin precio</span>
                                @endif
                            </p>
                        </div>

                        <div>
                            <p class="text-sm font-medium text-gray-500">Fecha de Creación</p>
                            <p class="mt-1 text-sm text-gray-900">{{ $product->created_at->format('d/m/Y H:i') }}</p>
                        </div>

                        <div>
                            <p class="text-sm font-medium text-gray-500">Última Actualización</p>
                            <p class="mt-1 text-sm text-gray-900">{{ $product->updated_at->format('d/m/Y H:i') }}</p>
                        </div>
                    </div>

                    @if(Auth::user()->isGestor())
                        <div class="mt-6 flex space-x-3">
                            <a href="{{ route('products.edit', $product) }}" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700">
                                Editar
                            </a>
                            <form action="{{ route('products.destroy', $product) }}" method="POST" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700" onclick="return confirmDelete(event, '¿Está seguro de eliminar este producto?')">
                                    Eliminar
                                </button>
                            </form>
                        </div>
                    @endif
                </div>
            </div>

            @if($product->prices->count() > 0)
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold mb-4">Historial de Precios</h3>
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Precio</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Vigente Desde</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Estado</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($product->prices()->latest('vigente_desde')->get() as $price)
                                        <tr>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                {{ formatCurrency($price->precio, 2) }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                {{ $price->vigente_desde->format('d/m/Y') }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                                @if($product->currentPrice() && $product->currentPrice()->id === $price->id)
                                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                                        Actual
                                                    </span>
                                                @else
                                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                                                        Histórico
                                                    </span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            @endif

            @if($product->stockMovements->count() > 0)
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold mb-4">Movimientos de Stock Recientes</h3>
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Fecha</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tipo</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Cantidad</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Descripción</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($product->stockMovements()->latest()->take(10)->get() as $movement)
                                        <tr>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                {{ $movement->created_at->format('d/m/Y H:i') }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                                    @if($movement->tipo === 'entrada') bg-green-100 text-green-800
                                                    @else bg-red-100 text-red-800
                                                    @endif">
                                                    {{ ucfirst($movement->tipo) }}
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                {{ formatNumber($movement->cantidad, 2) }}
                                            </td>
                                            <td class="px-6 py-4 text-sm text-gray-900">
                                                {{ $movement->descripcion ?? 'N/A' }}
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
