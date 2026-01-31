<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Detalle de Bobina de Papel
            </h2>
            <a href="{{ route('paper-coils.index') }}" class="text-gray-600 hover:text-gray-900">
                Volver
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-semibold mb-4">Información de la Bobina</h3>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <p class="text-sm font-medium text-gray-500">ID</p>
                            <p class="mt-1 text-sm text-gray-900">{{ $paperCoil->id }}</p>
                        </div>

                        <div>
                            <p class="text-sm font-medium text-gray-500">Tipo de Papel</p>
                            <p class="mt-1 text-sm text-gray-900">{{ $paperCoil->tipo_papel }}</p>
                        </div>

                        <div>
                            <p class="text-sm font-medium text-gray-500">Ancho (cm)</p>
                            <p class="mt-1 text-sm text-gray-900">{{ formatNumber($paperCoil->ancho, 2) }}</p>
                        </div>

                        <div>
                            <p class="text-sm font-medium text-gray-500">Gramaje (g/m²)</p>
                            <p class="mt-1 text-sm text-gray-900">{{ formatNumber($paperCoil->gramaje, 2) }}</p>
                        </div>

                        <div>
                            <p class="text-sm font-medium text-gray-500">Peso Inicial (kg)</p>
                            <p class="mt-1 text-sm text-gray-900">{{ formatNumber($paperCoil->peso_inicial, 2) }}</p>
                        </div>

                        <div>
                            <p class="text-sm font-medium text-gray-500">Peso Actual (kg)</p>
                            <p class="mt-1 text-sm {{ $paperCoil->peso_actual <= $paperCoil->alerta_minima ? 'text-yellow-600 font-bold' : 'text-gray-900' }}">
                                {{ formatNumber($paperCoil->peso_actual, 2) }}
                                @if($paperCoil->peso_actual <= $paperCoil->alerta_minima)
                                    <span class="text-xs">(⚠ Alerta de peso bajo)</span>
                                @endif
                            </p>
                        </div>

                        <div>
                            <p class="text-sm font-medium text-gray-500">Alerta Mínima (kg)</p>
                            <p class="mt-1 text-sm text-gray-900">{{ formatNumber($paperCoil->alerta_minima, 2) }}</p>
                        </div>

                        <div>
                            <p class="text-sm font-medium text-gray-500">Consumo</p>
                            <p class="mt-1 text-sm text-gray-900">
                                {{ formatNumber($paperCoil->peso_inicial - $paperCoil->peso_actual, 2) }} kg
                                ({{ formatNumber((($paperCoil->peso_inicial - $paperCoil->peso_actual) / $paperCoil->peso_inicial) * 100, 1) }}%)
                            </p>
                        </div>

                        <div>
                            <p class="text-sm font-medium text-gray-500">Fecha de Registro</p>
                            <p class="mt-1 text-sm text-gray-900">{{ $paperCoil->created_at->format('d/m/Y H:i') }}</p>
                        </div>

                        <div>
                            <p class="text-sm font-medium text-gray-500">Última Actualización</p>
                            <p class="mt-1 text-sm text-gray-900">{{ $paperCoil->updated_at->format('d/m/Y H:i') }}</p>
                        </div>
                    </div>

                    <div class="mt-6 flex space-x-3">
                        <a href="{{ route('paper-coils.edit', $paperCoil) }}" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700">
                            Editar
                        </a>
                        <form action="{{ route('paper-coils.destroy', $paperCoil) }}" method="POST" class="inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700" onclick="return confirm('¿Está seguro de eliminar esta bobina?')">
                                Eliminar
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            @if($paperCoil->products->count() > 0)
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold mb-4">Productos que usan esta Bobina</h3>
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Producto</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tipo</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Consumo por Unidad</th>
                                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($paperCoil->products as $product)
                                        <tr>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $product->name }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ ucfirst(str_replace('_', ' ', $product->type)) }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                {{ formatNumber($product->pivot->consumo_por_unidad, 4) }} kg
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                                <a href="{{ route('products.show', $product) }}" class="text-blue-600 hover:text-blue-900">Ver</a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            @else
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <p class="text-gray-500 text-center">Esta bobina aún no está asignada a ningún producto.</p>
                    </div>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
