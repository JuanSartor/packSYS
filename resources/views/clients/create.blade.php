<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Crear Cliente
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <form method="POST" action="{{ route('clients.store') }}">
                        @csrf

                        <div class="mb-4">
                            <label for="nombre" class="block text-gray-700 text-sm font-bold mb-2">
                                Nombre <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="nombre" id="nombre" value="{{ old('nombre') }}"
                                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('nombre') border-red-500 @enderror"
                                required>
                            @error('nombre')
                                <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="telefono" class="block text-gray-700 text-sm font-bold mb-2">
                                Teléfono
                            </label>
                            <input type="text" name="telefono" id="telefono" value="{{ old('telefono') }}"
                                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('telefono') border-red-500 @enderror">
                            @error('telefono')
                                <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="email" class="block text-gray-700 text-sm font-bold mb-2">
                                Email
                            </label>
                            <input type="email" name="email" id="email" value="{{ old('email') }}"
                                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('email') border-red-500 @enderror">
                            @error('email')
                                <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="direccion" class="block text-gray-700 text-sm font-bold mb-2">
                                Dirección
                            </label>
                            <textarea name="direccion" id="direccion" rows="3"
                                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('direccion') border-red-500 @enderror">{{ old('direccion') }}</textarea>
                            @error('direccion')
                                <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <div class="flex items-center justify-between mb-2">
                                <label for="canal_id" class="block text-gray-700 text-sm font-bold">
                                    Canal <span class="text-red-500">*</span>
                                </label>
                                <button type="button" onclick="openCanalModal()" class="text-blue-600 hover:text-blue-800 text-sm font-semibold">
                                    + Agregar Canal
                                </button>
                            </div>
                            <select name="canal_id" id="canal_id"
                                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('canal_id') border-red-500 @enderror"
                                required>
                                <option value="">Seleccione un canal</option>
                                @foreach($canales as $canal)
                                    <option value="{{ $canal->id }}" {{ old('canal_id') == $canal->id ? 'selected' : '' }}>
                                        {{ $canal->descripcion }}
                                    </option>
                                @endforeach
                            </select>
                            @error('canal_id')
                                <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="flex items-center justify-between mt-6">
                            <a href="{{ route('clients.index') }}" class="text-gray-600 hover:text-gray-900">
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

    <!-- Modal para crear canal -->
    <div id="canalModal" class="hidden fixed inset-0 bg-gray-900 bg-opacity-50 overflow-y-auto h-full w-full z-50 flex items-center justify-center p-4">
        <div class="relative bg-white rounded-lg shadow-xl w-full max-w-lg mx-4">
            <div class="p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-xl font-semibold text-gray-900">Nuevo Canal</h3>
                    <button type="button" onclick="closeCanalModal()" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>

                <form id="canalForm">
                    @csrf
                    <div class="mb-6">
                        <label for="modal_canal_descripcion" class="block text-gray-700 text-sm font-bold mb-2">
                            Descripción <span class="text-red-500">*</span>
                        </label>
                        <input type="text" id="modal_canal_descripcion" name="descripcion"
                            class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline focus:border-blue-500"
                            required>
                        <p id="modal_canal_descripcion_error" class="text-red-500 text-xs italic mt-1 hidden"></p>
                    </div>

                    <div class="flex items-center justify-end space-x-3">
                        <button type="button" onclick="closeCanalModal()" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition-colors">
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
        function openCanalModal() {
            document.getElementById('canalModal').classList.remove('hidden');
            document.getElementById('modal_canal_descripcion').focus();
        }

        function closeCanalModal() {
            document.getElementById('canalModal').classList.add('hidden');
            document.getElementById('canalForm').reset();
            document.getElementById('modal_canal_descripcion_error').classList.add('hidden');
        }

        // Manejar el submit del formulario del modal
        document.getElementById('canalForm').addEventListener('submit', async function(e) {
            e.preventDefault();

            const formData = new FormData(this);
            const submitButton = this.querySelector('button[type="submit"]');
            submitButton.disabled = true;
            submitButton.textContent = 'Guardando...';

            try {
                const response = await fetch('{{ route('canales.store') }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json',
                    },
                    body: formData
                });

                const data = await response.json();

                if (response.ok) {
                    // Agregar el nuevo canal al select
                    const select = document.getElementById('canal_id');
                    const option = new Option(data.descripcion, data.id, true, true);
                    select.add(option);

                    // Cerrar modal
                    closeCanalModal();

                    // Mostrar mensaje de éxito
                    showSuccess('El canal se ha creado exitosamente');
                } else {
                    // Mostrar errores de validación
                    if (data.errors && data.errors.descripcion) {
                        const errorElement = document.getElementById('modal_canal_descripcion_error');
                        errorElement.textContent = data.errors.descripcion[0];
                        errorElement.classList.remove('hidden');
                    }
                }
            } catch (error) {
                console.error('Error:', error);
                showError('Ocurrió un error al crear el canal');
            } finally {
                submitButton.disabled = false;
                submitButton.textContent = 'Guardar';
            }
        });

        // Cerrar modal al hacer click fuera
        document.getElementById('canalModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeCanalModal();
            }
        });
    </script>
</x-app-layout>
