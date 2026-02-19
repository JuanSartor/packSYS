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
                                Descripcion
                            </label>
                            <textarea name="descripcion" id="descripcion" rows="3"
                                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('descripcion') border-red-500 @enderror">{{ old('descripcion', $product->descripcion) }}</textarea>
                            @error('descripcion')
                                <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label class="block text-gray-700 text-sm font-bold mb-2">
                                Tipo de Producto
                            </label>
                            <div class="bg-gray-50 border border-gray-300 rounded w-full py-2 px-3 text-gray-700">
                                {{ $product->productType ? $product->productType->nombre : 'Sin tipo' }}
                            </div>
                            <input type="hidden" name="product_type_id" value="{{ $product->product_type_id }}">
                        </div>

                        <div class="mb-4">
                            <label class="block text-gray-700 text-sm font-bold mb-2">
                                Proveedor
                            </label>
                            <div class="bg-gray-50 border border-gray-300 rounded w-full py-2 px-3 text-gray-700">
                                {{ $product->proveedor ? $product->proveedor->nombre : 'Sin proveedor' }}
                            </div>
                            <input type="hidden" name="proveedor_id" value="{{ $product->proveedor_id }}">
                        </div>

                        <div class="mb-4">
                            <label class="block text-gray-700 text-sm font-bold mb-2">
                                Unidad
                            </label>
                            <div class="bg-gray-50 border border-gray-300 rounded w-full py-2 px-3 text-gray-700">
                                {{ $product->unidad ? $product->unidad->descripcion : 'Sin unidad' }}
                            </div>
                            <input type="hidden" name="unidad_id" value="{{ $product->unidad_id }}">
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
                                Stock Minimo <span class="text-red-500">*</span>
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

                        {{-- Seccion Materias Primas --}}
                        <div class="mb-4 border-t pt-4" x-data="materiasPrimasForm()">
                            <h4 class="font-semibold text-gray-700 mb-3">Consumo de Materias Primas</h4>

                            <template x-for="(item, idx) in items" :key="idx">
                                <div class="mb-3 bg-gray-50 p-4 rounded border">
                                    <div class="flex gap-2 mb-2 items-end">
                                        <div class="flex-1">
                                            <label class="block text-gray-700 text-xs font-bold mb-1">Materia Prima</label>
                                            <select :name="'materias_items[' + idx + '][materia_prima_id]'"
                                                x-model="item.materia_prima_id"
                                                @change="onMateriaSelected(idx)"
                                                class="shadow border rounded w-full py-1 px-2 text-sm text-gray-700" required>
                                                <option value="">Seleccione...</option>
                                                <template x-for="mp in materiasPrimas" :key="mp.id">
                                                    <option :value="mp.id" x-text="mp.nombre + ' (' + mp.unidad_consumo + ') - Stock: ' + mp.stock_actual"></option>
                                                </template>
                                            </select>
                                        </div>
                                        <button type="button" @click="removeItem(idx)"
                                            class="text-red-500 hover:text-red-700 text-sm font-bold px-2 py-1">X</button>
                                    </div>

                                    {{-- Campos de producto dinamicos segun MP seleccionada --}}
                                    <template x-if="getCamposProducto(item.materia_prima_id).length > 0">
                                        <div class="grid grid-cols-3 gap-3 mt-2 pt-2 border-t">
                                            <template x-for="campo in getCamposProducto(item.materia_prima_id)" :key="campo.nombre">
                                                <div>
                                                    <label class="block text-gray-700 text-xs font-bold mb-1">
                                                        <span x-text="campo.etiqueta"></span>
                                                        <span x-show="campo.obligatorio" class="text-red-500">*</span>
                                                    </label>
                                                    <input :type="campo.tipo === 'number' ? 'number' : 'text'"
                                                        :name="'materias_config[' + item.materia_prima_id + '][' + campo.nombre + ']'"
                                                        :step="campo.step || (campo.tipo === 'number' ? '0.01' : '')"
                                                        :required="campo.obligatorio"
                                                        x-model="item.config[campo.nombre]"
                                                        @input="recalcular(idx)"
                                                        class="shadow appearance-none border rounded w-full py-1 px-2 text-sm text-gray-700">
                                                </div>
                                            </template>
                                        </div>
                                    </template>

                                    {{-- Formula y preview de consumo --}}
                                    <template x-if="getFormula(item.materia_prima_id)">
                                        <div class="mt-2 pt-2 border-t">
                                            <div class="flex items-center gap-4">
                                                <div class="flex-1">
                                                    <span class="text-xs text-gray-400">Formula:</span>
                                                    <span class="text-xs font-mono text-gray-500" x-text="getFormula(item.materia_prima_id)"></span>
                                                </div>
                                                <div class="text-right">
                                                    <span class="text-xs text-gray-400">Consumo x unidad:</span>
                                                    <span class="text-sm font-bold"
                                                        :class="item.consumoPreview !== null ? 'text-green-700' : 'text-red-500'"
                                                        x-text="item.consumoPreview !== null ? (parseFloat(item.consumoPreview).toFixed(4) + ' ' + getUnidadConsumo(item.materia_prima_id)) : 'Complete los campos'">
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                    </template>

                                    <template x-if="item.materia_prima_id && !getFormula(item.materia_prima_id)">
                                        <div class="mt-2 pt-2 border-t">
                                            <span class="text-xs text-red-500">Esta materia prima no tiene formula de consumo definida.</span>
                                        </div>
                                    </template>
                                </div>
                            </template>

                            <button type="button" @click="addItem()"
                                class="text-blue-600 hover:text-blue-800 text-sm font-semibold">
                                + Agregar Materia Prima
                            </button>
                        </div>

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

    @php
        $materiasPrimasJson = $materiasPrimas->map(fn($mp) => [
            'id' => $mp->id,
            'nombre' => $mp->nombre,
            'unidad_consumo' => $mp->unidad_consumo,
            'stock_actual' => $mp->stock_actual,
            'campos_producto' => $mp->campos_producto ?? [],
            'formula_consumo' => $mp->formula_consumo,
            'campos_valores' => $mp->campos_valores ?? [],
        ]);

        $itemsIniciales = [];
        foreach ($product->productoMateriasPrimas as $pmp) {
            $config = [];
            if ($product->materias_config && isset($product->materias_config[$pmp->materia_prima_id])) {
                $config = $product->materias_config[$pmp->materia_prima_id];
            }
            $itemsIniciales[] = [
                'materia_prima_id' => (string)$pmp->materia_prima_id,
                'config' => (object)$config,
                'consumoPreview' => null,
            ];
        }
    @endphp
    <script>
        function evaluateFormula(formula, variables) {
            if (!formula) return null;
            let expression = formula;
            const keys = Object.keys(variables).sort((a, b) => b.length - a.length);
            for (const key of keys) {
                const val = parseFloat(variables[key]);
                if (isNaN(val)) return null;
                expression = expression.replaceAll(key, String(val));
            }
            const sanitized = expression.replace(/\s+/g, '');
            if (!/^[\d\+\-\*\/\(\)\.]+$/.test(sanitized)) return null;
            try {
                const result = new Function('return ' + sanitized)();
                if (typeof result !== 'number' || isNaN(result) || !isFinite(result)) return null;
                return result;
            } catch(e) {
                return null;
            }
        }

        function materiasPrimasForm() {
            return {
                items: {!! json_encode($itemsIniciales) !!},
                materiasPrimas: {!! json_encode($materiasPrimasJson) !!},

                getCamposProducto(mpId) {
                    if (!mpId) return [];
                    const mp = this.materiasPrimas.find(m => m.id == mpId);
                    return mp ? (mp.campos_producto || []) : [];
                },

                getFormula(mpId) {
                    if (!mpId) return null;
                    const mp = this.materiasPrimas.find(m => m.id == mpId);
                    return mp ? mp.formula_consumo : null;
                },

                getUnidadConsumo(mpId) {
                    if (!mpId) return '';
                    const mp = this.materiasPrimas.find(m => m.id == mpId);
                    return mp ? mp.unidad_consumo : '';
                },

                onMateriaSelected(idx) {
                    this.items[idx].config = {};
                    this.items[idx].consumoPreview = null;
                },

                recalcular(idx) {
                    const item = this.items[idx];
                    const mp = this.materiasPrimas.find(m => m.id == item.materia_prima_id);
                    if (!mp || !mp.formula_consumo) {
                        item.consumoPreview = null;
                        return;
                    }
                    const variables = Object.assign({}, mp.campos_valores || {}, item.config || {});
                    item.consumoPreview = evaluateFormula(mp.formula_consumo, variables);
                },

                addItem() {
                    this.items.push({ materia_prima_id: '', config: {}, consumoPreview: null });
                },

                removeItem(idx) {
                    this.items.splice(idx, 1);
                },

                init() {
                    this.items.forEach((item, idx) => {
                        if (item.materia_prima_id) this.recalcular(idx);
                    });
                }
            };
        }
    </script>
</x-app-layout>
