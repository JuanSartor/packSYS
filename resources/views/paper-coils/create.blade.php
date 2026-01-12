<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Crear Bobina de Papel
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <form method="POST" action="{{ route('paper-coils.store') }}">
                        @csrf

                        <div class="mb-4">
                            <label for="tipo_papel" class="block text-gray-700 text-sm font-bold mb-2">
                                Tipo de Papel <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="tipo_papel" id="tipo_papel" value="{{ old('tipo_papel') }}"
                                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('tipo_papel') border-red-500 @enderror"
                                required>
                            @error('tipo_papel')
                                <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="ancho" class="block text-gray-700 text-sm font-bold mb-2">
                                Ancho (cm) <span class="text-red-500">*</span>
                            </label>
                            <input type="number" step="0.01" name="ancho" id="ancho" value="{{ old('ancho', 0) }}"
                                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('ancho') border-red-500 @enderror"
                                required>
                            @error('ancho')
                                <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="gramaje" class="block text-gray-700 text-sm font-bold mb-2">
                                Gramaje (g/m²) <span class="text-red-500">*</span>
                            </label>
                            <input type="number" step="0.01" name="gramaje" id="gramaje" value="{{ old('gramaje', 0) }}"
                                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('gramaje') border-red-500 @enderror"
                                required>
                            @error('gramaje')
                                <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="peso_inicial" class="block text-gray-700 text-sm font-bold mb-2">
                                Peso Inicial (kg) <span class="text-red-500">*</span>
                            </label>
                            <input type="number" step="0.01" name="peso_inicial" id="peso_inicial" value="{{ old('peso_inicial', 0) }}"
                                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('peso_inicial') border-red-500 @enderror"
                                required>
                            @error('peso_inicial')
                                <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="peso_actual" class="block text-gray-700 text-sm font-bold mb-2">
                                Peso Actual (kg) <span class="text-red-500">*</span>
                            </label>
                            <input type="number" step="0.01" name="peso_actual" id="peso_actual" value="{{ old('peso_actual', 0) }}"
                                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('peso_actual') border-red-500 @enderror"
                                required>
                            @error('peso_actual')
                                <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="alerta_minima" class="block text-gray-700 text-sm font-bold mb-2">
                                Alerta Mínima (kg) <span class="text-red-500">*</span>
                            </label>
                            <input type="number" step="0.01" name="alerta_minima" id="alerta_minima" value="{{ old('alerta_minima', 0) }}"
                                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('alerta_minima') border-red-500 @enderror"
                                required>
                            @error('alerta_minima')
                                <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="flex items-center justify-between mt-6">
                            <a href="{{ route('paper-coils.index') }}" class="text-gray-600 hover:text-gray-900">
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
</x-app-layout>
