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
                            <p class="text-sm font-medium text-gray-500">Tipo de Producto</p>
                            <p class="mt-1 text-sm text-gray-900">{{ $product->productType ? $product->productType->nombre : 'N/A' }}</p>
                        </div>

                        <div>
                            <p class="text-sm font-medium text-gray-500">Unidad</p>
                            <p class="mt-1 text-sm text-gray-900">{{ $product->unidad ? $product->unidad->descripcion : 'N/A' }}</p>
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

                        @if($product->productoMateriasPrimas->count() > 0)
                        <div class="col-span-2">
                            <p class="text-sm font-medium text-gray-500 mb-2">Materias Primas Vinculadas</p>
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200 text-sm">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Materia Prima</th>
                                            <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Unidad</th>
                                            <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Formula</th>
                                            <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Consumo x Unidad</th>
                                            <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Stock Disponible</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-200">
                                        @foreach($product->productoMateriasPrimas as $pmp)
                                            <tr>
                                                <td class="px-3 py-2 text-gray-900">{{ $pmp->materiaPrima->nombre }}</td>
                                                <td class="px-3 py-2 text-gray-500 text-xs">{{ $pmp->materiaPrima->unidad_consumo }}</td>
                                                <td class="px-3 py-2 text-gray-500 text-xs font-mono">{{ $pmp->materiaPrima->formula_consumo ?? 'Sin formula' }}</td>
                                                <td class="px-3 py-2 text-gray-900 font-medium">{{ formatNumber($pmp->consumo_por_unidad, 4) }} {{ $pmp->materiaPrima->unidad_consumo }}</td>
                                                <td class="px-3 py-2 {{ $pmp->materiaPrima->stock_actual <= ($pmp->materiaPrima->alerta_minima ?? 0) ? 'text-red-600 font-bold' : 'text-gray-900' }}">
                                                    {{ formatNumber($pmp->materiaPrima->stock_actual, 2) }}
                                                </td>
                                            </tr>
                                            @if($product->materias_config && isset($product->materias_config[$pmp->materia_prima_id]))
                                                <tr class="bg-gray-50">
                                                    <td colspan="5" class="px-3 py-1">
                                                        <div class="flex gap-3 text-xs text-gray-600">
                                                            <span class="text-gray-400">Variables:</span>
                                                            @foreach($product->materias_config[$pmp->materia_prima_id] as $campo => $valor)
                                                                <span>{{ ucfirst(str_replace('_', ' ', $campo)) }} = {{ $valor }}</span>
                                                            @endforeach
                                                        </div>
                                                    </td>
                                                </tr>
                                            @endif
                                        @endforeach
                                    </tbody>
                                </table>
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
