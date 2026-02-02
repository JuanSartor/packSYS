<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Crear Producto
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            @if($similarProduct)
                <div class="mb-4 bg-blue-100 border border-blue-400 text-blue-700 px-4 py-3 rounded">
                    <p class="font-semibold">Creando producto similar a: {{ $similarProduct->name }}</p>
                    <p class="text-sm">Los datos del producto original han sido precargados. Modifica lo necesario y guarda.</p>
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <form method="POST" action="{{ route('products.store') }}">
                        @csrf

                        <div class="mb-4">
                            <label for="name" class="block text-gray-700 text-sm font-bold mb-2">
                                Nombre <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="name" id="name" value="{{ old('name', $similarProduct ? $similarProduct->name : '') }}"
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
                                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('descripcion') border-red-500 @enderror">{{ old('descripcion', $similarProduct ? $similarProduct->descripcion : '') }}</textarea>
                            @error('descripcion')
                                <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <div class="flex items-center justify-between mb-2">
                                <label for="product_type_id" class="block text-gray-700 text-sm font-bold">
                                    Tipo de Producto <span class="text-red-500">*</span>
                                </label>
                                <button type="button" onclick="openProductTypeModal()" class="text-blue-600 hover:text-blue-800 text-sm font-semibold">
                                    + Agregar Tipo
                                </button>
                            </div>
                            <select name="product_type_id" id="product_type_id"
                                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('product_type_id') border-red-500 @enderror"
                                required>
                                <option value="">Seleccione un tipo</option>
                                @foreach($productTypes as $productType)
                                    <option value="{{ $productType->id }}" {{ old('product_type_id', $similarProduct ? $similarProduct->product_type_id : '') == $productType->id ? 'selected' : '' }}>
                                        {{ $productType->nombre }}
                                    </option>
                                @endforeach
                            </select>
                            @error('product_type_id')
                                <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <div class="flex items-center justify-between mb-2">
                                <label for="proveedor_id" class="block text-gray-700 text-sm font-bold">
                                    Proveedor <span class="text-red-500">*</span>
                                </label>
                                <button type="button" onclick="openProveedorModal()" class="text-blue-600 hover:text-blue-800 text-sm font-semibold">
                                    + Agregar Proveedor
                                </button>
                            </div>
                            <select name="proveedor_id" id="proveedor_id"
                                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('proveedor_id') border-red-500 @enderror"
                                required>
                                <option value="">Seleccione un proveedor</option>
                                @foreach($proveedores as $proveedor)
                                    <option value="{{ $proveedor->id }}" {{ old('proveedor_id', $similarProduct ? $similarProduct->proveedor_id : '') == $proveedor->id ? 'selected' : '' }}>
                                        {{ $proveedor->nombre }}
                                    </option>
                                @endforeach
                            </select>
                            @error('proveedor_id')
                                <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <div class="flex items-center justify-between mb-2">
                                <label for="unidad_id" class="block text-gray-700 text-sm font-bold">
                                    Unidad <span class="text-red-500">*</span>
                                </label>
                                <button type="button" onclick="openUnidadModal()" class="text-blue-600 hover:text-blue-800 text-sm font-semibold">
                                    + Agregar Unidad
                                </button>
                            </div>
                            <select name="unidad_id" id="unidad_id"
                                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('unidad_id') border-red-500 @enderror"
                                required>
                                <option value="">Seleccione una unidad</option>
                                @foreach($unidades as $unidad)
                                    <option value="{{ $unidad->id }}" {{ old('unidad_id', $similarProduct ? $similarProduct->unidad_id : '') == $unidad->id ? 'selected' : '' }}>
                                        {{ $unidad->descripcion }}
                                    </option>
                                @endforeach
                            </select>
                            @error('unidad_id')
                                <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="stock_actual" class="block text-gray-700 text-sm font-bold mb-2">
                                Stock Actual <span class="text-red-500">*</span>
                            </label>
                            <input type="number" step="0.01" name="stock_actual" id="stock_actual" value="{{ old('stock_actual', $similarProduct ? $similarProduct->stock_actual : 0) }}"
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
                            <input type="number" step="0.01" name="stock_minimo" id="stock_minimo" value="{{ old('stock_minimo', $similarProduct ? $similarProduct->stock_minimo : 0) }}"
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
                            <input type="number" step="0.01" name="costo" id="costo" value="{{ old('costo', $similarProduct && $similarProduct->currentPrice() ? $similarProduct->currentPrice()->costo : 0) }}"
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
                            <input type="number" step="0.01" name="precio_venta" id="precio_venta" value="{{ old('precio_venta', $similarProduct && $similarProduct->currentPrice() ? $similarProduct->currentPrice()->precio_venta : 0) }}"
                                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('precio_venta') border-red-500 @enderror"
                                required>
                            @error('precio_venta')
                                <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label class="flex items-center">
                                <input type="checkbox" name="usa_bobina" id="usa_bobina" value="1" {{ old('usa_bobina', $similarProduct ? $similarProduct->usa_bobina : false) ? 'checked' : '' }}
                                    class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                                <span class="ml-2 text-sm text-gray-700">Este producto usa bobina de papel</span>
                            </label>
                            @error('usa_bobina')
                                <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div id="dimensiones-container" class="mb-4 {{ old('usa_bobina', $similarProduct ? $similarProduct->usa_bobina : false) ? '' : 'hidden' }}">
                            <div class="bg-gray-50 p-4 rounded">
                                <h4 class="font-semibold text-gray-700 mb-3">Dimensiones del Producto</h4>

                                <div class="grid grid-cols-3 gap-4">
                                    <div>
                                        <label for="ancho" class="block text-gray-700 text-sm font-bold mb-2">
                                            Ancho (cm) <span class="text-red-500 dimension-required">*</span>
                                        </label>
                                        <input type="number" step="0.01" name="ancho" id="ancho" value="{{ old('ancho', $similarProduct ? $similarProduct->ancho : '') }}"
                                            class="dimension-field shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('ancho') border-red-500 @enderror">
                                        @error('ancho')
                                            <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                                        @enderror
                                    </div>

                                    <div>
                                        <label for="largo" class="block text-gray-700 text-sm font-bold mb-2">
                                            Largo (cm) <span class="text-red-500 dimension-required">*</span>
                                        </label>
                                        <input type="number" step="0.01" name="largo" id="largo" value="{{ old('largo', $similarProduct ? $similarProduct->largo : '') }}"
                                            class="dimension-field shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('largo') border-red-500 @enderror">
                                        @error('largo')
                                            <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                                        @enderror
                                    </div>

                                    <div>
                                        <label for="fuelle" class="block text-gray-700 text-sm font-bold mb-2">
                                            Fuelle (cm) <span class="text-red-500 dimension-required">*</span>
                                        </label>
                                        <input type="number" step="0.01" name="fuelle" id="fuelle" value="{{ old('fuelle', $similarProduct ? $similarProduct->fuelle : '') }}"
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

    <!-- Modal para crear tipo de producto -->
    <div id="productTypeModal" class="hidden fixed inset-0 bg-gray-900 bg-opacity-50 overflow-y-auto h-full w-full z-50 flex items-center justify-center p-4">
        <div class="relative bg-white rounded-lg shadow-xl w-full max-w-lg mx-4">
            <div class="p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-xl font-semibold text-gray-900">Nuevo Tipo de Producto</h3>
                    <button type="button" onclick="closeProductTypeModal()" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>

                <form id="productTypeForm">
                    @csrf
                    <div class="mb-4">
                        <label for="modal_nombre" class="block text-gray-700 text-sm font-bold mb-2">
                            Nombre <span class="text-red-500">*</span>
                        </label>
                        <input type="text" id="modal_nombre" name="nombre"
                            class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline focus:border-blue-500"
                            required>
                        <p id="modal_nombre_error" class="text-red-500 text-xs italic mt-1 hidden"></p>
                    </div>

                    <div class="mb-6">
                        <label for="modal_descripcion" class="block text-gray-700 text-sm font-bold mb-2">
                            Descripción
                        </label>
                        <textarea id="modal_descripcion" name="descripcion" rows="3"
                            class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline focus:border-blue-500"></textarea>
                    </div>

                    <div class="flex items-center justify-end space-x-3">
                        <button type="button" onclick="closeProductTypeModal()" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition-colors">
                            Cancelar
                        </button>
                        <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                            Guardar
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal para crear proveedor -->
    <div id="proveedorModal" class="hidden fixed inset-0 bg-gray-900 bg-opacity-50 overflow-y-auto h-full w-full z-50 flex items-center justify-center p-4">
        <div class="relative bg-white rounded-lg shadow-xl w-full max-w-lg mx-4">
            <div class="p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-xl font-semibold text-gray-900">Nuevo Proveedor</h3>
                    <button type="button" onclick="closeProveedorModal()" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>

                <form id="proveedorForm">
                    @csrf
                    <div class="mb-4">
                        <label for="modal_proveedor_nombre" class="block text-gray-700 text-sm font-bold mb-2">
                            Nombre <span class="text-red-500">*</span>
                        </label>
                        <input type="text" id="modal_proveedor_nombre" name="nombre"
                            class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline focus:border-blue-500"
                            required>
                        <p id="modal_proveedor_nombre_error" class="text-red-500 text-xs italic mt-1 hidden"></p>
                    </div>

                    <div class="mb-6">
                        <label for="modal_proveedor_descripcion" class="block text-gray-700 text-sm font-bold mb-2">
                            Descripción
                        </label>
                        <textarea id="modal_proveedor_descripcion" name="descripcion" rows="3"
                            class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline focus:border-blue-500"></textarea>
                    </div>

                    <div class="flex items-center justify-end space-x-3">
                        <button type="button" onclick="closeProveedorModal()" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition-colors">
                            Cancelar
                        </button>
                        <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                            Guardar
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal para crear unidad -->
    <div id="unidadModal" class="hidden fixed inset-0 bg-gray-900 bg-opacity-50 overflow-y-auto h-full w-full z-50 flex items-center justify-center p-4">
        <div class="relative bg-white rounded-lg shadow-xl w-full max-w-lg mx-4">
            <div class="p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-xl font-semibold text-gray-900">Nueva Unidad</h3>
                    <button type="button" onclick="closeUnidadModal()" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>

                <form id="unidadForm">
                    @csrf
                    <div class="mb-6">
                        <label for="modal_unidad_descripcion" class="block text-gray-700 text-sm font-bold mb-2">
                            Descripción <span class="text-red-500">*</span>
                        </label>
                        <input type="text" id="modal_unidad_descripcion" name="descripcion"
                            class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline focus:border-blue-500"
                            required>
                        <p id="modal_unidad_descripcion_error" class="text-red-500 text-xs italic mt-1 hidden"></p>
                    </div>

                    <div class="flex items-center justify-end space-x-3">
                        <button type="button" onclick="closeUnidadModal()" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition-colors">
                            Cancelar
                        </button>
                        <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                            Guardar
                        </button>
                    </div>
                </form>
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

        // Funciones para el modal de tipo de producto
        function openProductTypeModal() {
            document.getElementById('productTypeModal').classList.remove('hidden');
            document.getElementById('modal_nombre').focus();
        }

        function closeProductTypeModal() {
            document.getElementById('productTypeModal').classList.add('hidden');
            document.getElementById('productTypeForm').reset();
            document.getElementById('modal_nombre_error').classList.add('hidden');
        }

        // Manejar el submit del formulario del modal
        document.getElementById('productTypeForm').addEventListener('submit', async function(e) {
            e.preventDefault();

            const formData = new FormData(this);
            const submitButton = this.querySelector('button[type="submit"]');
            submitButton.disabled = true;
            submitButton.textContent = 'Guardando...';

            try {
                const response = await fetch('{{ route('product-types.store') }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json',
                    },
                    body: formData
                });

                const data = await response.json();

                if (response.ok) {
                    // Agregar el nuevo tipo al select
                    const select = document.getElementById('product_type_id');
                    const option = new Option(data.nombre, data.id, true, true);
                    select.add(option);

                    // Cerrar modal
                    closeProductTypeModal();

                    // Mostrar mensaje de éxito
                    showSuccess('El tipo de producto se ha creado exitosamente');
                } else {
                    // Mostrar errores de validación
                    if (data.errors && data.errors.nombre) {
                        const errorElement = document.getElementById('modal_nombre_error');
                        errorElement.textContent = data.errors.nombre[0];
                        errorElement.classList.remove('hidden');
                    }
                }
            } catch (error) {
                console.error('Error:', error);
                showError('Ocurrió un error al crear el tipo de producto');
            } finally {
                submitButton.disabled = false;
                submitButton.textContent = 'Guardar';
            }
        });

        // Cerrar modal al hacer click fuera
        document.getElementById('productTypeModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeProductTypeModal();
            }
        });

        // Funciones para el modal de Proveedor
        function openProveedorModal() {
            document.getElementById('proveedorModal').classList.remove('hidden');
            document.getElementById('modal_proveedor_nombre').focus();
        }

        function closeProveedorModal() {
            document.getElementById('proveedorModal').classList.add('hidden');
            document.getElementById('proveedorForm').reset();
            document.getElementById('modal_proveedor_nombre_error').classList.add('hidden');
        }

        // Manejar el submit del formulario del modal de proveedor
        document.getElementById('proveedorForm').addEventListener('submit', async function(e) {
            e.preventDefault();

            const formData = new FormData(this);
            const submitButton = this.querySelector('button[type="submit"]');
            submitButton.disabled = true;
            submitButton.textContent = 'Guardando...';

            try {
                const response = await fetch('{{ route('proveedores.store') }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json',
                    },
                    body: formData
                });

                const data = await response.json();

                if (response.ok) {
                    // Agregar el nuevo proveedor al select
                    const select = document.getElementById('proveedor_id');
                    const option = new Option(data.nombre, data.id, true, true);
                    select.add(option);

                    // Cerrar modal
                    closeProveedorModal();

                    // Mostrar mensaje de éxito
                    showSuccess('El proveedor se ha creado exitosamente');
                } else {
                    // Mostrar errores de validación
                    if (data.errors && data.errors.nombre) {
                        const errorElement = document.getElementById('modal_proveedor_nombre_error');
                        errorElement.textContent = data.errors.nombre[0];
                        errorElement.classList.remove('hidden');
                    }
                }
            } catch (error) {
                console.error('Error:', error);
                showError('Ocurrió un error al crear el proveedor');
            } finally {
                submitButton.disabled = false;
                submitButton.textContent = 'Guardar';
            }
        });

        // Cerrar modal de proveedor al hacer click fuera
        document.getElementById('proveedorModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeProveedorModal();
            }
        });

        // Funciones para el modal de Unidad
        function openUnidadModal() {
            document.getElementById('unidadModal').classList.remove('hidden');
            document.getElementById('modal_unidad_descripcion').focus();
        }

        function closeUnidadModal() {
            document.getElementById('unidadModal').classList.add('hidden');
            document.getElementById('unidadForm').reset();
            document.getElementById('modal_unidad_descripcion_error').classList.add('hidden');
        }

        // Manejar el submit del formulario del modal de unidad
        document.getElementById('unidadForm').addEventListener('submit', async function(e) {
            e.preventDefault();

            const formData = new FormData(this);
            const submitButton = this.querySelector('button[type="submit"]');
            submitButton.disabled = true;
            submitButton.textContent = 'Guardando...';

            try {
                const response = await fetch('{{ route('unidades.store') }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json',
                    },
                    body: formData
                });

                const data = await response.json();

                if (response.ok) {
                    // Agregar la nueva unidad al select
                    const select = document.getElementById('unidad_id');
                    const option = new Option(data.descripcion, data.id, true, true);
                    select.add(option);

                    // Cerrar modal
                    closeUnidadModal();

                    // Mostrar mensaje de éxito
                    showSuccess('La unidad se ha creado exitosamente');
                } else {
                    // Mostrar errores de validación
                    if (data.errors && data.errors.descripcion) {
                        const errorElement = document.getElementById('modal_unidad_descripcion_error');
                        errorElement.textContent = data.errors.descripcion[0];
                        errorElement.classList.remove('hidden');
                    }
                }
            } catch (error) {
                console.error('Error:', error);
                showError('Ocurrió un error al crear la unidad');
            } finally {
                submitButton.disabled = false;
                submitButton.textContent = 'Guardar';
            }
        });

        // Cerrar modal de unidad al hacer click fuera
        document.getElementById('unidadModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeUnidadModal();
            }
        });
    </script>
</x-app-layout>
