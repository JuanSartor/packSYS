<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Editar Producto
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <form method="POST" action="{{ route('products.update', $product) }}">
                        @csrf
                        @method('PUT')

                        <div class="mb-4">
                            <label for="name" class="block text-gray-700 text-sm font-bold mb-2">
                                Nombre <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="name" id="name" value="{{ old('name', $product->name) }}"
                                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('name') border-red-500 @enderror"
                                required>
                            @error('name')
                                <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="descripcion" class="block text-gray-700 text-sm font-bold mb-2">
                                Descripción
                            </label>
                            <textarea name="descripcion" id="descripcion" rows="3"
                                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('descripcion') border-red-500 @enderror">{{ old('descripcion', $product->descripcion) }}</textarea>
                            @error('descripcion')
                                <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="type" class="block text-gray-700 text-sm font-bold mb-2">
                                Tipo <span class="text-red-500">*</span>
                            </label>
                            <select name="type" id="type"
                                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('type') border-red-500 @enderror"
                                required>
                                <option value="">Seleccione un tipo</option>
                                <option value="bolsa_papel" {{ old('type', $product->type) == 'bolsa_papel' ? 'selected' : '' }}>Bolsa de Papel</option>
                                <option value="friselina" {{ old('type', $product->type) == 'friselina' ? 'selected' : '' }}>Friselina</option>
                                <option value="caja" {{ old('type', $product->type) == 'caja' ? 'selected' : '' }}>Caja</option>
                                <option value="insumo" {{ old('type', $product->type) == 'insumo' ? 'selected' : '' }}>Insumo</option>
                            </select>
                            @error('type')
                                <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="unidad" class="block text-gray-700 text-sm font-bold mb-2">
                                Unidad <span class="text-red-500">*</span>
                            </label>
                            <select name="unidad" id="unidad"
                                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('unidad') border-red-500 @enderror"
                                required>
                                <option value="">Seleccione una unidad</option>
                                <option value="unidad" {{ old('unidad', $product->unidad) == 'unidad' ? 'selected' : '' }}>Unidad</option>
                                <option value="kg" {{ old('unidad', $product->unidad) == 'kg' ? 'selected' : '' }}>Kilogramo (kg)</option>
                                <option value="metro" {{ old('unidad', $product->unidad) == 'metro' ? 'selected' : '' }}>Metro</option>
                            </select>
                            @error('unidad')
                                <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="stock_actual" class="block text-gray-700 text-sm font-bold mb-2">
                                Stock Actual <span class="text-red-500">*</span>
                            </label>
                            <input type="number" step="0.01" name="stock_actual" id="stock_actual" value="{{ old('stock_actual', $product->stock_actual) }}"
                                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('stock_actual') border-red-500 @enderror"
                                required>
                            @error('stock_actual')
                                <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="stock_minimo" class="block text-gray-700 text-sm font-bold mb-2">
                                Stock Mínimo <span class="text-red-500">*</span>
                            </label>
                            <input type="number" step="0.01" name="stock_minimo" id="stock_minimo" value="{{ old('stock_minimo', $product->stock_minimo) }}"
                                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('stock_minimo') border-red-500 @enderror"
                                required>
                            @error('stock_minimo')
                                <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="costo" class="block text-gray-700 text-sm font-bold mb-2">
                                Costo <span class="text-red-500">*</span>
                            </label>
                            <input type="number" step="0.01" name="costo" id="costo" value="{{ old('costo', $product->currentPrice() ? $product->currentPrice()->costo : 0) }}"
                                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('costo') border-red-500 @enderror"
                                required>
                            @error('costo')
                                <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="precio_venta" class="block text-gray-700 text-sm font-bold mb-2">
                                Precio de Venta <span class="text-red-500">*</span>
                            </label>
                            <input type="number" step="0.01" name="precio_venta" id="precio_venta" value="{{ old('precio_venta', $product->currentPrice() ? $product->currentPrice()->precio_venta : 0) }}"
                                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('precio_venta') border-red-500 @enderror"
                                required>
                            @error('precio_venta')
                                <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        @if($product->usa_bobina)
                        <div class="mb-4">
                            <div class="bg-blue-50 p-4 rounded">
                                <p class="text-sm font-medium text-gray-700 mb-2">
                                    <span class="inline-flex items-center">
                                        <svg class="w-5 h-5 text-blue-600 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                        </svg>
                                        Este producto usa bobina de papel
                                    </span>
                                </p>

                                @if($product->ancho || $product->largo || $product->fuelle)
                                <div class="mt-3 pt-3 border-t border-blue-200">
                                    <p class="text-sm font-semibold text-gray-700 mb-2">Dimensiones:</p>
                                    <div class="grid grid-cols-3 gap-4 text-sm text-gray-900">
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
                            </div>
                        </div>
                        @endif

                        <!-- Campos hidden para mantener los valores originales -->
                        <input type="hidden" name="usa_bobina" value="{{ $product->usa_bobina ? 1 : 0 }}">
                        <input type="hidden" name="ancho" value="{{ $product->ancho }}">
                        <input type="hidden" name="largo" value="{{ $product->largo }}">
                        <input type="hidden" name="fuelle" value="{{ $product->fuelle }}">

                        <div class="flex items-center justify-between mt-6">
                            <a href="{{ route('products.index') }}" class="text-gray-600 hover:text-gray-900">
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
