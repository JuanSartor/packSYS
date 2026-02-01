<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Detalle del Cliente
            </h2>
            <a href="{{ route('clients.index') }}" class="text-gray-600 hover:text-gray-900">
                Volver
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <h3 class="text-lg font-semibold mb-4">Información del Cliente</h3>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <p class="text-sm font-medium text-gray-500">ID</p>
                            <p class="mt-1 text-sm text-gray-900">{{ $client->id }}</p>
                        </div>

                        <div>
                            <p class="text-sm font-medium text-gray-500">Nombre</p>
                            <p class="mt-1 text-sm text-gray-900">{{ $client->nombre }}</p>
                        </div>

                        <div>
                            <p class="text-sm font-medium text-gray-500">Teléfono</p>
                            <p class="mt-1 text-sm text-gray-900">{{ $client->telefono ?? 'N/A' }}</p>
                        </div>

                        <div>
                            <p class="text-sm font-medium text-gray-500">Email</p>
                            <p class="mt-1 text-sm text-gray-900">{{ $client->email ?? 'N/A' }}</p>
                        </div>

                        <div class="col-span-2">
                            <p class="text-sm font-medium text-gray-500">Dirección</p>
                            <p class="mt-1 text-sm text-gray-900">{{ $client->direccion ?? 'N/A' }}</p>
                        </div>

                        <div>
                            <p class="text-sm font-medium text-gray-500">Fecha de Registro</p>
                            <p class="mt-1 text-sm text-gray-900">{{ $client->created_at->format('d/m/Y H:i') }}</p>
                        </div>

                        <div>
                            <p class="text-sm font-medium text-gray-500">Última Actualización</p>
                            <p class="mt-1 text-sm text-gray-900">{{ $client->updated_at->format('d/m/Y H:i') }}</p>
                        </div>
                    </div>

                    <div class="mt-6 flex space-x-3">
                        <a href="{{ route('clients.edit', $client) }}" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700">
                            Editar
                        </a>
                        <form action="{{ route('clients.destroy', $client) }}" method="POST" class="inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700" onclick="return confirmDelete(event, '¿Está seguro de eliminar este cliente?')">
                                Eliminar
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            @if($client->sales->count() > 0)
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-semibold mb-4">Historial de Ventas</h3>
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">ID</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Total</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Vendedor</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Fecha</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($client->sales as $sale)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">#{{ $sale->id }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ formatCurrency($sale->total, 2) }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $sale->creator->name }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $sale->created_at->format('d/m/Y') }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <a href="{{ route('sales.show', $sale) }}" class="text-blue-600 hover:text-blue-900">Ver</a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>

                    <div class="mt-4">
                        <p class="text-sm text-gray-600">
                            Total de ventas: <span class="font-semibold">{{ formatCurrency($client->sales->sum('total'), 2) }}</span>
                        </p>
                    </div>
                </div>
            </div>
            @else
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <p class="text-gray-500 text-center">Este cliente aún no tiene ventas registradas.</p>
                </div>
            </div>
            @endif
        </div>
    </div>
</x-app-layout>