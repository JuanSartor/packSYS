<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Crear Producto
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <form method="POST" action="{{ route('products.store') }}">
                        @csrf

                        <div class="mb-4">
                            <label for="name" class="block text-gray-700 text-sm font-bold mb-2">
                                Nombre <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="name" id="name" value="{{ old('name') }}"
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
                                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('descripcion') border-red-500 @enderror">{{ old('descripcion') }}</textarea>
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
                                <option value="bolsa_papel" {{ old('type') == 'bolsa_papel' ? 'selected' : '' }}>Bolsa de Papel</option>
                                <option value="friselina" {{ old('type') == 'friselina' ? 'selected' : '' }}>Friselina</option>
                                <option value="caja" {{ old('type') == 'caja' ? 'selected' : '' }}>Caja</option>
                                <option value="insumo" {{ old('type') == 'insumo' ? 'selected' : '' }}>Insumo</option>
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
                                <option value="unidad" {{ old('unidad') == 'unidad' ? 'selected' : '' }}>Unidad</option>
                                <option value="kg" {{ old('unidad') == 'kg' ? 'selected' : '' }}>Kilogramo (kg)</option>
                                <option value="metro" {{ old('unidad') == 'metro' ? 'selected' : '' }}>Metro</option>
                            </select>
                            @error('unidad')
                                <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="stock_actual" class="block text-gray-700 text-sm font-bold mb-2">
                                Stock Actual <span class="text-red-500">*</span>
                            </label>
                            <input type="number" step="0.01" name="stock_actual" id="stock_actual" value="{{ old('stock_actual', 0) }}"
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
                            <input type="number" step="0.01" name="stock_minimo" id="stock_minimo" value="{{ old('stock_minimo', 0) }}"
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
                            <input type="number" step="0.01" name="costo" id="costo" value="{{ old('costo', 0) }}"
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
                            <input type="number" step="0.01" name="precio_venta" id="precio_venta" value="{{ old('precio_venta', 0) }}"
                                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('precio_venta') border-red-500 @enderror"
                                required>
                            @error('precio_venta')
                                <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label class="flex items-center">
                                <input type="checkbox" name="usa_bobina" id="usa_bobina" value="1" {{ old('usa_bobina') ? 'checked' : '' }}
                                    class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                                <span class="ml-2 text-sm text-gray-700">Este producto usa bobina de papel</span>
                            </label>
                            @error('usa_bobina')
                                <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div id="dimensiones-container" class="mb-4 hidden">
                            <div class="bg-gray-50 p-4 rounded">
                                <h4 class="font-semibold text-gray-700 mb-3">Dimensiones del Producto</h4>

                                <div class="grid grid-cols-3 gap-4">
                                    <div>
                                        <label for="ancho" class="block text-gray-700 text-sm font-bold mb-2">
                                            Ancho (cm) <span class="text-red-500 dimension-required">*</span>
                                        </label>
                                        <input type="number" step="0.01" name="ancho" id="ancho" value="{{ old('ancho') }}"
                                            class="dimension-field shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('ancho') border-red-500 @enderror">
                                        @error('ancho')
                                            <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                                        @enderror
                                    </div>

                                    <div>
                                        <label for="largo" class="block text-gray-700 text-sm font-bold mb-2">
                                            Largo (cm) <span class="text-red-500 dimension-required">*</span>
                                        </label>
                                        <input type="number" step="0.01" name="largo" id="largo" value="{{ old('largo') }}"
                                            class="dimension-field shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('largo') border-red-500 @enderror">
                                        @error('largo')
                                            <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                                        @enderror
                                    </div>

                                    <div>
                                        <label for="fuelle" class="block text-gray-700 text-sm font-bold mb-2">
                                            Fuelle (cm) <span class="text-red-500 dimension-required">*</span>
                                        </label>
                                        <input type="number" step="0.01" name="fuelle" id="fuelle" value="{{ old('fuelle') }}"
                                            class="dimension-field shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('fuelle') border-red-500 @enderror">
                                        @error('fuelle')
                                            <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="flex items-center justify-between mt-6">
                            <a href="{{ route('products.index') }}" class="text-gray-600 hover:text-gray-900">
                                Cancelar
                            </a>
                            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                                Guardar
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const usaBobinaCheckbox = document.getElementById('usa_bobina');
            const dimensionesContainer = document.getElementById('dimensiones-container');
            const dimensionFields = document.querySelectorAll('.dimension-field');

            function toggleDimensiones() {
                if (usaBobinaCheckbox.checked) {
                    dimensionesContainer.classList.remove('hidden');
                    dimensionFields.forEach(field => {
                        field.required = true;
                    });
                } else {
                    dimensionesContainer.classList.add('hidden');
                    dimensionFields.forEach(field => {
                        field.required = false;
                        field.value = '';
                    });
                }
            }

            // Ejecutar al cargar la página
            toggleDimensiones();

            // Ejecutar cuando cambie el checkbox
            usaBobinaCheckbox.addEventListener('change', toggleDimensiones);
        });
    </script>
</x-app-layout>
