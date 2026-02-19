<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Editar Materia Prima
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <form method="POST" action="{{ route('materias-primas.update', $materiaPrima) }}" x-data="materiaPrimaForm()">
                        @csrf
                        @method('PUT')

                        {{-- Informacion basica --}}
                        <div class="mb-4">
                            <label for="nombre" class="block text-gray-700 text-sm font-bold mb-2">
                                Nombre <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="nombre" id="nombre" value="{{ old('nombre', $materiaPrima->nombre) }}"
                                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('nombre') border-red-500 @enderror"
                                required>
                            @error('nombre')
                                <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="descripcion" class="block text-gray-700 text-sm font-bold mb-2">
                                Descripcion
                            </label>
                            <textarea name="descripcion" id="descripcion" rows="2"
                                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('descripcion') border-red-500 @enderror">{{ old('descripcion', $materiaPrima->descripcion) }}</textarea>
                            @error('descripcion')
                                <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="unidad_consumo" class="block text-gray-700 text-sm font-bold mb-2">
                                Unidad de Consumo <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="unidad_consumo" id="unidad_consumo" value="{{ old('unidad_consumo', $materiaPrima->unidad_consumo) }}"
                                placeholder="ej: kg, unidad, litro, metro"
                                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('unidad_consumo') border-red-500 @enderror"
                                required>
                            @error('unidad_consumo')
                                <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="formula_consumo" class="block text-gray-700 text-sm font-bold mb-2">
                                Formula de Consumo
                            </label>
                            <textarea name="formula_consumo" id="formula_consumo" rows="2"
                                placeholder="ej: (ancho * largo) / 1000"
                                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('formula_consumo') border-red-500 @enderror">{{ old('formula_consumo', $materiaPrima->formula_consumo) }}</textarea>
                            @error('formula_consumo')
                                <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Variables de consumo materia prima --}}
                        <div class="mb-6 border-t pt-4">
                            <h3 class="text-lg font-semibold text-gray-700 mb-2">Variables de consumo materia prima</h3>
                            <p class="text-sm text-gray-500 mb-3">Campos personalizados para esta materia prima.</p>

                            <template x-for="(campo, index) in camposInventario" :key="index">
                                <div class="flex flex-wrap gap-2 mb-3 items-end bg-gray-50 p-3 rounded border">
                                    <div class="flex-1 min-w-[150px]">
                                        <label class="text-xs font-bold text-gray-600">Nombre</label>
                                        <input type="text" x-model="campo.etiqueta"
                                            :name="'campos_inventario['+index+'][etiqueta]'"
                                            class="shadow appearance-none border rounded w-full py-1 px-2 text-sm text-gray-700" required>
                                    </div>
                                    <div class="flex-1 min-w-[150px]">
                                        <label class="text-xs font-bold text-gray-600">Descripcion</label>
                                        <input type="text" x-model="campo.descripcion"
                                            :name="'campos_inventario['+index+'][descripcion]'"
                                            class="shadow appearance-none border rounded w-full py-1 px-2 text-sm text-gray-700">
                                    </div>
                                    <div class="w-28">
                                        <label class="text-xs font-bold text-gray-600">Tipo</label>
                                        <select x-model="campo.tipo"
                                            :name="'campos_inventario['+index+'][tipo]'"
                                            class="shadow border rounded w-full py-1 px-2 text-sm text-gray-700">
                                            <option value="text">Texto</option>
                                            <option value="number">Numero</option>
                                            <option value="textarea">Area texto</option>
                                        </select>
                                    </div>
                                    <div class="w-20" x-show="campo.tipo === 'number'">
                                        <label class="text-xs font-bold text-gray-600">Step</label>
                                        <input type="text" x-model="campo.step"
                                            :name="'campos_inventario['+index+'][step]'"
                                            placeholder="0.01"
                                            class="shadow appearance-none border rounded w-full py-1 px-2 text-sm text-gray-700">
                                    </div>
                                    <div class="flex items-center gap-1">
                                        <input type="hidden" :name="'campos_inventario['+index+'][obligatorio]'" :value="campo.obligatorio ? '1' : '0'">
                                        <label class="text-xs font-bold text-gray-600 flex items-center gap-1 cursor-pointer">
                                            <input type="checkbox" x-model="campo.obligatorio"
                                                class="rounded border-gray-300 text-blue-600">
                                            Req.
                                        </label>
                                    </div>
                                    <button type="button" @click="camposInventario.splice(index, 1)"
                                        class="text-red-500 hover:text-red-700 text-sm font-bold px-2 py-1">
                                        X
                                    </button>
                                </div>
                            </template>

                            <button type="button" @click="agregarCampoInventario()"
                                class="text-blue-600 hover:text-blue-800 text-sm font-semibold">
                                + Agregar Variable
                            </button>
                        </div>

                        {{-- Valores de las variables --}}
                        <div x-show="camposInventario.length > 0" x-transition class="mb-6 border-t pt-4">
                            <h3 class="text-lg font-semibold text-gray-700 mb-2">Valores de las Variables</h3>

                            <template x-for="(campo, index) in camposInventario" :key="'val_'+index">
                                <div class="mb-4" x-show="campo.etiqueta">
                                    <label class="block text-gray-700 text-sm font-bold mb-2">
                                        <span x-text="campo.etiqueta"></span>
                                        <span x-show="campo.obligatorio" class="text-red-500">*</span>
                                        <span x-show="campo.descripcion" class="text-xs text-gray-400 font-normal ml-1" x-text="'- ' + campo.descripcion"></span>
                                    </label>
                                    <input x-show="campo.tipo === 'text' || campo.tipo === 'number'"
                                        :type="campo.tipo"
                                        :name="'campo_' + slugify(campo.etiqueta)"
                                        :value="getValor(campo)"
                                        :step="campo.step || (campo.tipo === 'number' ? '0.01' : undefined)"
                                        :required="campo.obligatorio"
                                        class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                                    <textarea x-show="campo.tipo === 'textarea'"
                                        :name="'campo_' + slugify(campo.etiqueta)"
                                        :required="campo.obligatorio"
                                        rows="3"
                                        class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" x-text="getValor(campo)"></textarea>
                                </div>
                            </template>
                        </div>

                        {{-- Campos de Producto --}}
                        <div class="mb-6 border-t pt-4">
                            <h3 class="text-lg font-semibold text-gray-700 mb-2">Campos de Producto</h3>
                            <p class="text-sm text-gray-500 mb-3">Campos que apareceran en el formulario de producto cuando se use esta materia prima.</p>

                            <template x-for="(campo, index) in camposProducto" :key="index">
                                <div class="flex flex-wrap gap-2 mb-3 items-end bg-blue-50 p-3 rounded border border-blue-200">
                                    <div class="flex-1 min-w-[150px]">
                                        <label class="text-xs font-bold text-gray-600">Nombre</label>
                                        <input type="text" x-model="campo.etiqueta"
                                            :name="'campos_producto['+index+'][etiqueta]'"
                                            class="shadow appearance-none border rounded w-full py-1 px-2 text-sm text-gray-700" required>
                                    </div>
                                    <div class="flex-1 min-w-[150px]">
                                        <label class="text-xs font-bold text-gray-600">Descripcion</label>
                                        <input type="text" x-model="campo.descripcion"
                                            :name="'campos_producto['+index+'][descripcion]'"
                                            class="shadow appearance-none border rounded w-full py-1 px-2 text-sm text-gray-700">
                                    </div>
                                    <div class="w-28">
                                        <label class="text-xs font-bold text-gray-600">Tipo</label>
                                        <select x-model="campo.tipo"
                                            :name="'campos_producto['+index+'][tipo]'"
                                            class="shadow border rounded w-full py-1 px-2 text-sm text-gray-700">
                                            <option value="text">Texto</option>
                                            <option value="number">Numero</option>
                                            <option value="textarea">Area texto</option>
                                        </select>
                                    </div>
                                    <div class="w-20" x-show="campo.tipo === 'number'">
                                        <label class="text-xs font-bold text-gray-600">Step</label>
                                        <input type="text" x-model="campo.step"
                                            :name="'campos_producto['+index+'][step]'"
                                            placeholder="0.01"
                                            class="shadow appearance-none border rounded w-full py-1 px-2 text-sm text-gray-700">
                                    </div>
                                    <div class="flex items-center gap-1">
                                        <input type="hidden" :name="'campos_producto['+index+'][obligatorio]'" :value="campo.obligatorio ? '1' : '0'">
                                        <label class="text-xs font-bold text-gray-600 flex items-center gap-1 cursor-pointer">
                                            <input type="checkbox" x-model="campo.obligatorio"
                                                class="rounded border-gray-300 text-blue-600">
                                            Req.
                                        </label>
                                    </div>
                                    <button type="button" @click="camposProducto.splice(index, 1)"
                                        class="text-red-500 hover:text-red-700 text-sm font-bold px-2 py-1">
                                        X
                                    </button>
                                </div>
                            </template>

                            <button type="button" @click="agregarCampoProducto()"
                                class="text-blue-600 hover:text-blue-800 text-sm font-semibold">
                                + Agregar Campo de Producto
                            </button>
                        </div>

                        {{-- Stock --}}
                        <div class="border-t pt-4">
                            <h3 class="text-lg font-semibold text-gray-700 mb-4">Stock</h3>

                            <div class="grid grid-cols-3 gap-4">
                                <div class="mb-4">
                                    <label for="stock_inicial" class="block text-gray-700 text-sm font-bold mb-2">
                                        Stock Inicial <span class="text-red-500">*</span>
                                    </label>
                                    <input type="number" step="0.01" name="stock_inicial" id="stock_inicial" value="{{ old('stock_inicial', $materiaPrima->stock_inicial) }}"
                                        class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('stock_inicial') border-red-500 @enderror"
                                        required>
                                    @error('stock_inicial')
                                        <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div class="mb-4">
                                    <label for="stock_actual" class="block text-gray-700 text-sm font-bold mb-2">
                                        Stock Actual <span class="text-red-500">*</span>
                                    </label>
                                    <input type="number" step="0.01" name="stock_actual" id="stock_actual" value="{{ old('stock_actual', $materiaPrima->stock_actual) }}"
                                        class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('stock_actual') border-red-500 @enderror"
                                        required>
                                    @error('stock_actual')
                                        <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div class="mb-4">
                                    <label for="alerta_minima" class="block text-gray-700 text-sm font-bold mb-2">
                                        Alerta Minima <span class="text-red-500">*</span>
                                    </label>
                                    <input type="number" step="0.01" name="alerta_minima" id="alerta_minima" value="{{ old('alerta_minima', $materiaPrima->alerta_minima) }}"
                                        class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('alerta_minima') border-red-500 @enderror"
                                        required>
                                    @error('alerta_minima')
                                        <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="flex items-center justify-between mt-6">
                            <a href="{{ route('materias-primas.index') }}" class="text-gray-600 hover:text-gray-900">
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

    @php
        $camposInv = old('campos_inventario', $materiaPrima->campos_inventario ?? []);
        $camposProd = old('campos_producto', $materiaPrima->campos_producto ?? []);
        $camposValoresData = $materiaPrima->campos_valores ?? [];
    @endphp
    <script>
        function materiaPrimaForm() {
            return {
                camposInventario: {!! json_encode($camposInv) !!},
                camposProducto: {!! json_encode($camposProd) !!},
                camposValores: {!! json_encode($camposValoresData) !!},

                agregarCampoInventario() {
                    this.camposInventario.push({ etiqueta: '', descripcion: '', tipo: 'text', obligatorio: true, step: null });
                },
                agregarCampoProducto() {
                    this.camposProducto.push({ etiqueta: '', descripcion: '', tipo: 'text', obligatorio: true, step: null });
                },
                slugify(text) {
                    if (!text) return '';
                    return text.toLowerCase()
                        .normalize('NFD').replace(/[\u0300-\u036f]/g, '')
                        .replace(/[^a-z0-9]+/g, '_')
                        .replace(/^_+|_+$/g, '');
                },
                getValor(campo) {
                    const key = campo.nombre || this.slugify(campo.etiqueta);
                    return this.camposValores[key] || '';
                }
            };
        }
    </script>
</x-app-layout>
