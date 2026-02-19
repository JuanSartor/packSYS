<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Detalle de Materia Prima
            </h2>
            <div class="space-x-2">
                <a href="{{ route('materias-primas.edit', $materiaPrima) }}" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                    Editar
                </a>
                <a href="{{ route('materias-primas.index') }}" class="text-gray-600 hover:text-gray-900">
                    Volver
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-semibold mb-4">Informacion General</h3>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <p class="text-sm font-medium text-gray-500">ID</p>
                            <p class="mt-1 text-sm text-gray-900">{{ $materiaPrima->id }}</p>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-500">Nombre</p>
                            <p class="mt-1 text-sm text-gray-900">{{ $materiaPrima->nombre }}</p>
                        </div>
                        @if($materiaPrima->descripcion)
                        <div class="col-span-2">
                            <p class="text-sm font-medium text-gray-500">Descripcion</p>
                            <p class="mt-1 text-sm text-gray-900">{{ $materiaPrima->descripcion }}</p>
                        </div>
                        @endif
                        <div>
                            <p class="text-sm font-medium text-gray-500">Unidad de Consumo</p>
                            <p class="mt-1 text-sm text-gray-900">{{ $materiaPrima->unidad_consumo }}</p>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-500">Stock Actual</p>
                            <p class="mt-1 text-sm {{ $materiaPrima->stock_actual <= $materiaPrima->alerta_minima && $materiaPrima->alerta_minima > 0 ? 'text-red-600 font-bold' : 'text-gray-900' }}">
                                {{ formatNumber($materiaPrima->stock_actual, 2) }}
                            </p>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-500">Stock Inicial</p>
                            <p class="mt-1 text-sm text-gray-900">{{ formatNumber($materiaPrima->stock_inicial, 2) }}</p>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-500">Alerta Minima</p>
                            <p class="mt-1 text-sm text-gray-900">{{ formatNumber($materiaPrima->alerta_minima, 2) }}</p>
                        </div>
                        @if($materiaPrima->formula_consumo)
                        <div class="col-span-2">
                            <p class="text-sm font-medium text-gray-500">Formula de Consumo</p>
                            <p class="mt-1 text-sm text-gray-900 font-mono bg-gray-50 p-2 rounded">{{ $materiaPrima->formula_consumo }}</p>
                        </div>
                        @endif
                        <div>
                            <p class="text-sm font-medium text-gray-500">Fecha de Creacion</p>
                            <p class="mt-1 text-sm text-gray-900">{{ $materiaPrima->created_at->format('d/m/Y H:i') }}</p>
                        </div>
                        @if($materiaPrima->creator)
                        <div>
                            <p class="text-sm font-medium text-gray-500">Creado por</p>
                            <p class="mt-1 text-sm text-gray-900">{{ $materiaPrima->creator->name }}</p>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Variables de consumo (valores) --}}
            @if(count($materiaPrima->campos_inventario ?? []) > 0 && !empty($materiaPrima->campos_valores))
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-semibold mb-4">Variables de Consumo</h3>
                    <div class="grid grid-cols-2 gap-4">
                        @foreach($materiaPrima->campos_inventario as $campo)
                            <div>
                                <p class="text-sm font-medium text-gray-500">{{ $campo['etiqueta'] }}</p>
                                @if(!empty($campo['descripcion']))
                                    <p class="text-xs text-gray-400">{{ $campo['descripcion'] }}</p>
                                @endif
                                <p class="mt-1 text-sm text-gray-900">{{ $materiaPrima->campos_valores[$campo['nombre']] ?? '-' }}</p>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
            @endif

            {{-- Campos de Producto definidos --}}
            @if(count($materiaPrima->campos_producto ?? []) > 0)
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-semibold mb-4">Campos de Producto</h3>
                    <p class="text-sm text-gray-500 mb-3">Campos que se muestran al vincular esta materia prima con un producto.</p>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 text-sm">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Nombre</th>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Descripcion</th>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Tipo</th>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Obligatorio</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                @foreach($materiaPrima->campos_producto as $campo)
                                    <tr>
                                        <td class="px-4 py-2 text-gray-900">{{ $campo['etiqueta'] }}</td>
                                        <td class="px-4 py-2 text-gray-500">{{ $campo['descripcion'] ?? '-' }}</td>
                                        <td class="px-4 py-2 text-gray-900">{{ ucfirst($campo['tipo']) }}</td>
                                        <td class="px-4 py-2">
                                            @if($campo['obligatorio'])
                                                <span class="text-green-600 font-semibold">Si</span>
                                            @else
                                                <span class="text-gray-400">No</span>
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

            {{-- Productos vinculados --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-semibold mb-4">Productos que usan esta Materia Prima</h3>
                    @if($materiaPrima->productos->count() > 0)
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Producto</th>
                                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Consumo por Unidad</th>
                                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200">
                                    @foreach($materiaPrima->productos as $producto)
                                        <tr>
                                            <td class="px-4 py-2 text-sm text-gray-900">{{ $producto->name }}</td>
                                            <td class="px-4 py-2 text-sm text-gray-900">{{ formatNumber($producto->pivot->consumo_por_unidad, 4) }}</td>
                                            <td class="px-4 py-2 text-sm font-medium">
                                                <a href="{{ route('products.show', $producto) }}" class="text-blue-600 hover:text-blue-900">Ver</a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="text-gray-500 text-sm">No hay productos usando esta materia prima.</p>
                    @endif
                </div>
            </div>

            {{-- Movimientos de stock --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-semibold mb-4">Ultimos Movimientos de Stock</h3>
                    @if($materiaPrima->stockMovements->count() > 0)
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Fecha</th>
                                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Tipo</th>
                                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Cantidad</th>
                                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Referencia</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200">
                                    @foreach($materiaPrima->stockMovements as $mov)
                                        <tr>
                                            <td class="px-4 py-2 text-sm text-gray-900">{{ $mov->created_at->format('d/m/Y H:i') }}</td>
                                            <td class="px-4 py-2 text-sm">
                                                <span class="px-2 py-1 rounded text-xs font-medium {{ $mov->tipo === 'entrada' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                                    {{ ucfirst($mov->tipo) }}
                                                </span>
                                            </td>
                                            <td class="px-4 py-2 text-sm text-gray-900">{{ formatNumber($mov->cantidad, 2) }}</td>
                                            <td class="px-4 py-2 text-sm text-gray-900">{{ $mov->referencia ?? '-' }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="text-gray-500 text-sm">No hay movimientos de stock registrados.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
