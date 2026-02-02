<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Crear Orden de Producción
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <form method="POST" action="{{ route('production-orders.store') }}">
                        @csrf

                        <div class="mb-4">
                            <label for="product_id" class="block text-gray-700 text-sm font-bold mb-2">
                                Producto <span class="text-red-500">*</span>
                            </label>
                            <select name="product_id" id="product_id"
                                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('product_id') border-red-500 @enderror"
                                required>
                                <option value="">Seleccione un producto</option>
                                @foreach($products as $product)
                                    <option value="{{ $product->id }}" {{ old('product_id') == $product->id ? 'selected' : '' }}>
                                        {{ $product->name }}
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
                            <input type="number" step="0.01" name="cantidad" id="cantidad" value="{{ old('cantidad', 0) }}"
                                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('cantidad') border-red-500 @enderror"
                                required>
                            @error('cantidad')
                                <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <div class="flex items-center justify-between mb-2">
                                <label for="order_status_id" class="block text-gray-700 text-sm font-bold">
                                    Estado <span class="text-red-500">*</span>
                                </label>
                                <button type="button" onclick="openOrderStatusModal()" class="text-blue-600 hover:text-blue-800 text-sm font-semibold">
                                    + Agregar Estado
                                </button>
                            </div>
                            <select name="order_status_id" id="order_status_id"
                                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('order_status_id') border-red-500 @enderror"
                                required>
                                <option value="">Seleccione un estado</option>
                                @foreach($orderStatuses as $status)
                                    <option value="{{ $status->id }}" {{ old('order_status_id') == $status->id ? 'selected' : '' }}>
                                        {{ $status->nombre }}
                                    </option>
                                @endforeach
                            </select>
                            @error('order_status_id')
                                <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="flex items-center justify-between mt-6">
                            <a href="{{ route('production-orders.index') }}" class="text-gray-600 hover:text-gray-900">
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

    <!-- Modal para crear estado de orden -->
    <div id="orderStatusModal" class="hidden fixed inset-0 bg-gray-900 bg-opacity-50 overflow-y-auto h-full w-full z-50 flex items-center justify-center p-4">
        <div class="relative bg-white rounded-lg shadow-xl w-full max-w-lg mx-4">
            <div class="p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-xl font-semibold text-gray-900">Nuevo Estado de Orden</h3>
                    <button type="button" onclick="closeOrderStatusModal()" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>

                <form id="orderStatusForm">
                    @csrf
                    <div class="mb-4">
                        <label for="modal_status_nombre" class="block text-gray-700 text-sm font-bold mb-2">
                            Nombre <span class="text-red-500">*</span>
                        </label>
                        <input type="text" id="modal_status_nombre" name="nombre"
                            class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline focus:border-blue-500"
                            required>
                        <p id="modal_status_nombre_error" class="text-red-500 text-xs italic mt-1 hidden"></p>
                    </div>

                    <div class="mb-6">
                        <label for="modal_status_descripcion" class="block text-gray-700 text-sm font-bold mb-2">
                            Descripción
                        </label>
                        <textarea id="modal_status_descripcion" name="descripcion" rows="3"
                            class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline focus:border-blue-500"></textarea>
                        <p id="modal_status_descripcion_error" class="text-red-500 text-xs italic mt-1 hidden"></p>
                    </div>

                    <div class="flex items-center justify-end space-x-3">
                        <button type="button" onclick="closeOrderStatusModal()" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition-colors">
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
        // Funciones para el modal de Estado de Orden
        function openOrderStatusModal() {
            document.getElementById('orderStatusModal').classList.remove('hidden');
            document.getElementById('modal_status_nombre').focus();
        }

        function closeOrderStatusModal() {
            document.getElementById('orderStatusModal').classList.add('hidden');
            document.getElementById('orderStatusForm').reset();
            document.getElementById('modal_status_nombre_error').classList.add('hidden');
            document.getElementById('modal_status_descripcion_error').classList.add('hidden');
        }

        // Manejar el submit del formulario del modal
        document.getElementById('orderStatusForm').addEventListener('submit', async function(e) {
            e.preventDefault();

            const formData = new FormData(this);
            const submitButton = this.querySelector('button[type="submit"]');
            submitButton.disabled = true;
            submitButton.textContent = 'Guardando...';

            // Ocultar errores previos
            document.getElementById('modal_status_nombre_error').classList.add('hidden');
            document.getElementById('modal_status_descripcion_error').classList.add('hidden');

            try {
                const response = await fetch('{{ route('order-statuses.store') }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json',
                    },
                    body: formData
                });

                const data = await response.json();

                if (response.ok) {
                    // Agregar el nuevo estado al select
                    const select = document.getElementById('order_status_id');
                    const option = new Option(data.nombre, data.id, true, true);
                    select.add(option);

                    // Cerrar modal
                    closeOrderStatusModal();

                    // Mostrar mensaje de éxito
                    showSuccess('El estado se ha creado exitosamente');
                } else {
                    // Mostrar errores de validación
                    if (data.errors) {
                        if (data.errors.nombre) {
                            const errorElement = document.getElementById('modal_status_nombre_error');
                            errorElement.textContent = data.errors.nombre[0];
                            errorElement.classList.remove('hidden');
                        }
                        if (data.errors.descripcion) {
                            const errorElement = document.getElementById('modal_status_descripcion_error');
                            errorElement.textContent = data.errors.descripcion[0];
                            errorElement.classList.remove('hidden');
                        }
                    }
                }
            } catch (error) {
                console.error('Error:', error);
                showError('Ocurrió un error al crear el estado');
            } finally {
                submitButton.disabled = false;
                submitButton.textContent = 'Guardar';
            }
        });

        // Cerrar modal al hacer click fuera
        document.getElementById('orderStatusModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeOrderStatusModal();
            }
        });
    </script>
</x-app-layout>
