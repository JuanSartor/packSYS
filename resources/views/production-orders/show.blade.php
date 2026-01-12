<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Orden de Producción #{{ $productionOrder->id }}
            </h2>
            <a href="{{ route('production-orders.index') }}" class="text-gray-600 hover:text-gray-900">
                Volver
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-6">
            @if(session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
                    {{ session('error') }}
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-semibold mb-4">Información de la Orden</h3>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <p class="text-sm font-medium text-gray-500">ID</p>
                            <p class="mt-1 text-sm text-gray-900">#{{ $productionOrder->id }}</p>
                        </div>

                        <div>
                            <p class="text-sm font-medium text-gray-500">Producto</p>
                            <p class="mt-1 text-sm text-gray-900">
                                <a href="{{ route('products.show', $productionOrder->product) }}" class="text-blue-600 hover:text-blue-900">
                                    {{ $productionOrder->product->name }}
                                </a>
                            </p>
                        </div>

                        <div>
                            <p class="text-sm font-medium text-gray-500">Cantidad</p>
                            <p class="mt-1 text-sm text-gray-900">{{ number_format($productionOrder->cantidad, 2) }}</p>
                        </div>

                        <div>
                            <p class="text-sm font-medium text-gray-500">Estado</p>
                            <p class="mt-1">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                    @if($productionOrder->estado === 'finalizada') bg-green-100 text-green-800
                                    @elseif($productionOrder->estado === 'produccion') bg-blue-100 text-blue-800
                                    @elseif($productionOrder->estado === 'pausada') bg-yellow-100 text-yellow-800
                                    @elseif($productionOrder->estado === 'pendiente') bg-orange-100 text-orange-800
                                    @else bg-gray-100 text-gray-800
                                    @endif">
                                    {{ ucfirst($productionOrder->estado) }}
                                </span>
                            </p>
                        </div>

                        <div>
                            <p class="text-sm font-medium text-gray-500">Creado por</p>
                            <p class="mt-1 text-sm text-gray-900">{{ $productionOrder->creator->name }}</p>
                        </div>

                        <div>
                            <p class="text-sm font-medium text-gray-500">Fecha de Creación</p>
                            <p class="mt-1 text-sm text-gray-900">{{ $productionOrder->created_at->format('d/m/Y H:i') }}</p>
                        </div>

                        @if($productionOrder->started_at)
                            <div>
                                <p class="text-sm font-medium text-gray-500">Iniciado</p>
                                <p class="mt-1 text-sm text-gray-900">{{ $productionOrder->started_at->format('d/m/Y H:i') }}</p>
                            </div>
                        @endif

                        @if($productionOrder->finished_at)
                            <div>
                                <p class="text-sm font-medium text-gray-500">Finalizado</p>
                                <p class="mt-1 text-sm text-gray-900">{{ $productionOrder->finished_at->format('d/m/Y H:i') }}</p>
                            </div>
                        @endif

                        @if($productionOrder->started_at && $productionOrder->finished_at)
                            <div class="col-span-2">
                                <p class="text-sm font-medium text-gray-500">Tiempo Total de Producción</p>
                                <p class="mt-1 text-sm text-gray-900">
                                    {{ $productionOrder->started_at->diffForHumans($productionOrder->finished_at, true) }}
                                </p>
                            </div>
                        @endif
                    </div>

                    @if($productionOrder->estado !== 'finalizada')
                        <div class="mt-6 flex space-x-3">
                            @if($productionOrder->estado !== 'produccion')
                                <form action="{{ route('production-orders.start', $productionOrder) }}" method="POST" class="inline">
                                    @csrf
                                    <button type="submit" class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700">
                                        Iniciar Producción
                                    </button>
                                </form>
                            @endif

                            @if($productionOrder->estado === 'produccion')
                                <form action="{{ route('production-orders.pause', $productionOrder) }}" method="POST" class="inline">
                                    @csrf
                                    <button type="submit" class="inline-flex items-center px-4 py-2 bg-yellow-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-yellow-700">
                                        Pausar
                                    </button>
                                </form>
                            @endif

                            @if(in_array($productionOrder->estado, ['produccion', 'pausada']))
                                <form action="{{ route('production-orders.finish', $productionOrder) }}" method="POST" class="inline" onsubmit="return confirm('¿Está seguro de finalizar esta orden? Se incrementará el stock del producto.')">
                                    @csrf
                                    <button type="submit" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700">
                                        Finalizar
                                    </button>
                                </form>
                            @endif

                            <a href="{{ route('production-orders.edit', $productionOrder) }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700">
                                Editar
                            </a>

                            <form action="{{ route('production-orders.destroy', $productionOrder) }}" method="POST" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700" onclick="return confirm('¿Está seguro de eliminar esta orden?')">
                                    Eliminar
                                </button>
                            </form>
                        </div>
                    @else
                        <div class="mt-6">
                            <p class="text-sm text-gray-600">Esta orden ha sido finalizada.</p>
                        </div>
                    @endif
                </div>
            </div>

            @if($productionOrder->productionTimes->count() > 0)
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold mb-4">Tiempos de Producción</h3>
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Inicio</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Fin</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Duración</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($productionOrder->productionTimes as $time)
                                        <tr>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $time->started_at->format('d/m/Y H:i:s') }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                {{ $time->ended_at ? $time->ended_at->format('d/m/Y H:i:s') : 'En curso' }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                @if($time->ended_at)
                                                    {{ $time->started_at->diffForHumans($time->ended_at, true) }}
                                                @else
                                                    {{ $time->started_at->diffForHumans(now(), true) }} (en curso)
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
        </div>
    </div>
</x-app-layout>
