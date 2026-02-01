<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Venta #{{ $sale->id }}
            </h2>
            <a href="{{ route('sales.index') }}" class="text-gray-600 hover:text-gray-900">
                Volver
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-semibold mb-4">Información de la Venta</h3>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <p class="text-sm font-medium text-gray-500">ID</p>
                            <p class="mt-1 text-sm text-gray-900">#{{ $sale->id }}</p>
                        </div>

                        <div>
                            <p class="text-sm font-medium text-gray-500">Cliente</p>
                            <p class="mt-1 text-sm text-gray-900">
                                <a href="{{ route('clients.show', $sale->client) }}" class="text-blue-600 hover:text-blue-900">
                                    {{ $sale->client->nombre }}
                                </a>
                            </p>
                        </div>

                        <div>
                            <p class="text-sm font-medium text-gray-500">Vendedor</p>
                            <p class="mt-1 text-sm text-gray-900">{{ $sale->creator->name }}</p>
                        </div>

                        <div>
                            <p class="text-sm font-medium text-gray-500">Fecha</p>
                            <p class="mt-1 text-sm text-gray-900">{{ $sale->created_at->format('d/m/Y H:i') }}</p>
                        </div>

                        <div>
                            <p class="text-sm font-medium text-gray-500">Transporte</p>
                            <p class="mt-1 text-sm text-gray-900">
                                @if($sale->transport)
                                    {{ $sale->transport->nombre }} - {{ formatCurrency($sale->transport->costo, 2) }}
                                @else
                                    Sin transporte
                                @endif
                            </p>
                        </div>

                        <div>
                            <p class="text-sm font-medium text-gray-500">Total</p>
                            <p class="mt-1 text-lg font-bold text-gray-900">{{ formatCurrency($sale->total, 2) }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-semibold mb-4">Items de la Venta</h3>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Producto</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Cantidad</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Precio Unit.</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Subtotal</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($sale->items as $item)
                                    <tr>
                                        <td class="px-6 py-4 text-sm text-gray-900">
                                            <a href="{{ route('products.show', $item->product) }}" class="text-blue-600 hover:text-blue-900">
                                                {{ $item->product->name }}
                                            </a>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-right text-gray-900">
                                            {{ formatNumber($item->cantidad, 2) }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-right text-gray-900">
                                            {{ formatCurrency($item->precio_unitario_venta, 2) }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-right text-gray-900">
                                            {{ formatCurrency($item->cantidad * $item->precio_unitario_venta, 2) }}
                                        </td>
                                    </tr>
                                @endforeach

                                <tr class="bg-gray-50 font-semibold">
                                    <td colspan="3" class="px-6 py-4 text-sm text-right text-gray-900">Subtotal:</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-right text-gray-900">
                                        {{ formatCurrency($sale->items->sum(function($item) { return $item->cantidad * $item->precio_unitario_venta; }), 2) }}
                                    </td>
                                </tr>

                                @if($sale->transport)
                                    <tr class="bg-gray-50">
                                        <td colspan="3" class="px-6 py-4 text-sm text-right text-gray-900">Transporte:</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-right text-gray-900">
                                            {{ formatCurrency($sale->transport->costo, 2) }}
                                        </td>
                                    </tr>
                                @endif

                                <tr class="bg-gray-100 font-bold text-lg">
                                    <td colspan="3" class="px-6 py-4 text-sm text-right text-gray-900">TOTAL:</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-right text-gray-900">
                                        {{ formatCurrency($sale->total, 2) }}
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
