<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Nueva Venta
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            @if(session('error'))
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                {{ session('error') }}
            </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <form method="POST" action="{{ route('sales.store') }}" id="saleForm">
                        @csrf

                        <div class="mb-4">
                            <label for="client_id" class="block text-gray-700 text-sm font-bold mb-2">
                                Cliente <span class="text-red-500">*</span>
                            </label>
                            <select name="client_id" id="client_id"
                                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('client_id') border-red-500 @enderror"
                                required>
                                <option value="">Seleccione un cliente</option>
                                @foreach($clients as $client)
                                <option value="{{ $client->id }}" {{ old('client_id') == $client->id ? 'selected' : '' }}>
                                    {{ $client->nombre }}
                                </option>
                                @endforeach
                            </select>
                            @error('client_id')
                            <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mb-6">
                            <div class="flex justify-between items-center mb-3">
                                <label class="block text-gray-700 text-sm font-bold">
                                    Items de Venta <span class="text-red-500">*</span>
                                </label>
                                <button type="button" id="addItem" class="bg-green-600 hover:bg-green-700 text-white text-xs font-bold py-2 px-4 rounded">
                                    + Agregar Item
                                </button>
                            </div>

                            <div id="itemsContainer" class="space-y-3">
                                <!-- Items se agregan dinámicamente aquí -->
                            </div>
                        </div>

                        <div class="mb-4">
                            <label for="transport_id" class="block text-gray-700 text-sm font-bold mb-2">
                                Transporte <span class="text-red-500">*</span>
                            </label>
                            <select name="transport_id" id="transport_id"
                                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('transport_id') border-red-500 @enderror"
                                required>
                                <option value="">Seleccione un transporte</option>
                                @foreach($transports as $transport)
                                <option value="{{ $transport->id }}" data-cost="{{ $transport->costo }}" {{ old('transport_id') == $transport->id ? 'selected' : '' }}>
                                    {{ $transport->nombre }} - {{ formatCurrency($transport->costo) }}
                                </option>
                                @endforeach
                            </select>
                            @error('transport_id')
                                <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mb-6 p-4 bg-gray-50 rounded">
                            <div class="text-right">
                                <p class="text-lg font-bold text-gray-700">
                                    Total: $<span id="totalAmount">0.00</span>
                                </p>
                            </div>
                        </div>

                        <div class="flex items-center justify-between">
                            <a href="{{ route('sales.index') }}" class="text-gray-600 hover:text-gray-900">
                                Cancelar
                            </a>
                            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                                Crear Venta
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Datos de productos con precios
        const products = @json($productsData);

        let itemIndex = 0;

        // Agregar primer item al cargar
        document.addEventListener('DOMContentLoaded', function() {
            addItemRow();
        });

        // Botón agregar item
        document.getElementById('addItem').addEventListener('click', addItemRow);

        // Función para agregar una fila de item
        function addItemRow() {
            const container = document.getElementById('itemsContainer');
            const index = itemIndex++;

            const itemHtml = `
                <div class="item-row flex gap-2 items-start border p-3 rounded bg-white" data-index="${index}">
                    <div class="flex-1">
                        <select name="items[${index}][product_id]" class="product-select shadow border rounded w-full py-2 px-3 text-gray-700" required>
                            <option value="">Seleccionar producto</option>
                            ${products.map(p => '<option value="' + p.id + '" data-price="' + p.price + '" data-price-id="' + p.price_id + '" data-stock="' + p.stock + '">' + p.name + ' (Stock: ' + p.stock + ')</option>').join('')}
                        </select>
                        <input type="hidden" name="items[${index}][product_price_id]" class="price-id-input">
                    </div>
                    <div class="w-24">
                        <input type="number" step="0.01" name="items[${index}][cantidad]" placeholder="Cant." class="cantidad-input shadow border rounded w-full py-2 px-3 text-gray-700" min="0.01" required>
                    </div>
                    <div class="w-28">
                        <input type="number" step="0.01" name="items[${index}][precio_unitario]" placeholder="Precio" class="precio-input shadow border rounded w-full py-2 px-3 text-gray-700" min="0" required>
                    </div>
                    <div class="w-28">
                        <input type="text" class="subtotal-display shadow border rounded w-full py-2 px-3 text-gray-700 bg-gray-100" placeholder="$0.00" readonly>
                    </div>
                    <button type="button" class="remove-item bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-3 rounded">
                        ×
                    </button>
                </div>
            `;

            container.insertAdjacentHTML('beforeend', itemHtml);

            // Event listeners para la nueva fila
            const newRow = container.lastElementChild;

            newRow.querySelector('.product-select').addEventListener('change', function() {
                const option = this.options[this.selectedIndex];
                const price = option.dataset.price || 0;
                const priceId = option.dataset.priceId || '';
                newRow.querySelector('.precio-input').value = parseFloat(price).toFixed(2);
                newRow.querySelector('.price-id-input').value = priceId;
                calculateRowSubtotal(newRow);
            });

            newRow.querySelector('.cantidad-input').addEventListener('input', function() {
                calculateRowSubtotal(newRow);
            });

            newRow.querySelector('.precio-input').addEventListener('input', function() {
                calculateRowSubtotal(newRow);
            });

            newRow.querySelector('.remove-item').addEventListener('click', function() {
                if (document.querySelectorAll('.item-row').length > 1) {
                    newRow.remove();
                    calculateTotal();
                } else {
                    alert('Debe haber al menos un item en la venta');
                }
            });
        }

        // Calcular subtotal de una fila
        function calculateRowSubtotal(row) {
            const cantidad = parseFloat(row.querySelector('.cantidad-input').value) || 0;
            const precio = parseFloat(row.querySelector('.precio-input').value) || 0;
            const subtotal = cantidad * precio;

            row.querySelector('.subtotal-display').value = '$' + subtotal.toFixed(2);
            calculateTotal();
        }

        // Calcular total general
        function calculateTotal() {
            let total = 0;

            document.querySelectorAll('.item-row').forEach(row => {
                const cantidad = parseFloat(row.querySelector('.cantidad-input').value) || 0;
                const precio = parseFloat(row.querySelector('.precio-input').value) || 0;
                total += cantidad * precio;
            });

            // Agregar transporte
            const transportSelect = document.getElementById('transport_id');
            if (transportSelect.value) {
                const transportCost = parseFloat(transportSelect.options[transportSelect.selectedIndex].dataset.cost) || 0;
                total += transportCost;
            }

            document.getElementById('totalAmount').textContent = total.toFixed(2);
        }

        // Recalcular total al cambiar transporte
        document.getElementById('transport_id').addEventListener('change', calculateTotal);
    </script>
</x-app-layout>