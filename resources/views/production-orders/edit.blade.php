<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Editar Orden de Producción
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <form method="POST" action="{{ route('production-orders.update', $productionOrder) }}">
                        @csrf
                        @method('PUT')

                        <div class="mb-4">
                            <label for="product_id" class="block text-gray-700 text-sm font-bold mb-2">
                                Producto <span class="text-red-500">*</span>
                            </label>
                            <select name="product_id" id="product_id"
                                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('product_id') border-red-500 @enderror"
                                required>
                                <option value="">Seleccione un producto</option>
                                @foreach($products as $product)
                                    <option value="{{ $product->id }}" {{ old('product_id', $productionOrder->product_id) == $product->id ? 'selected' : '' }}>
                                        {{ $product->name }} ({{ ucfirst($product->type) }})
                                    </option>
                                @endforeach
                            </select>
                            @error('product_id')
                                <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="cantidad" class="block text-gray-700 text-sm font-bold mb-2">
                                Cantidad <span class="text-red-500">*</span>
                            </label>
                            <input type="number" step="0.01" name="cantidad" id="cantidad" value="{{ old('cantidad', $productionOrder->cantidad) }}"
                                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('cantidad') border-red-500 @enderror"
                                required>
                            @error('cantidad')
                                <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="estado" class="block text-gray-700 text-sm font-bold mb-2">
                                Estado <span class="text-red-500">*</span>
                            </label>
                            <select name="estado" id="estado"
                                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('estado') border-red-500 @enderror"
                                required>
                                <option value="">Seleccione un estado</option>
                                <option value="espera" {{ old('estado', $productionOrder->estado) == 'espera' ? 'selected' : '' }}>En Espera</option>
                                <option value="pendiente" {{ old('estado', $productionOrder->estado) == 'pendiente' ? 'selected' : '' }}>Pendiente</option>
                                <option value="produccion" {{ old('estado', $productionOrder->estado) == 'produccion' ? 'selected' : '' }}>En Producción</option>
                                <option value="pausada" {{ old('estado', $productionOrder->estado) == 'pausada' ? 'selected' : '' }}>Pausada</option>
                                <option value="finalizada" {{ old('estado', $productionOrder->estado) == 'finalizada' ? 'selected' : '' }}>Finalizada</option>
                            </select>
                            @error('estado')
                                <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="flex items-center justify-between mt-6">
                            <a href="{{ route('production-orders.index') }}" class="text-gray-600 hover:text-gray-900">
                                Cancelar
                            </a>
                            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                                Actualizar
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
