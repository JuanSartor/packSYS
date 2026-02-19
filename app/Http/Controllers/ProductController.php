<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductPrice;
use App\Models\ProductType;
use App\Models\ProductoMateriaPrima;
use App\Models\MateriaPrima;
use App\Models\Proveedor;
use App\Models\Unidad;
use App\Services\FormulaEvaluator;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index()
    {
        $search = request('search');

        $products = Product::where('eliminado', 0)
            ->when($search, function ($query, $search) {
                $query->where('name', 'like', "%{$search}%");
            })
            ->latest()
            ->paginate(15)
            ->appends(['search' => $search]);

        return view('products.index', compact('products'));
    }

    public function create()
    {
        $proveedores = Proveedor::where('eliminado', 0)->orderBy('nombre')->get();
        $productTypes = ProductType::where('eliminado', 0)->orderBy('nombre')->get();
        $unidades = Unidad::where('eliminado', 0)->orderBy('descripcion')->get();
        $materiasPrimas = MateriaPrima::where('eliminado', 0)->orderBy('nombre')->get();

        // Si viene un ID de producto similar, cargar sus datos
        $similarProduct = null;
        if (request('similar')) {
            $similarProduct = Product::with(['prices', 'productoMateriasPrimas.materiaPrima'])->find(request('similar'));
        }

        return view('products.create', compact('proveedores', 'productTypes', 'unidades', 'materiasPrimas', 'similarProduct'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'descripcion' => ['nullable', 'string'],
            'product_type_id' => ['required', 'exists:product_types,id'],
            'proveedor_id' => ['required', 'exists:proveedores,id'],
            'unidad_id' => ['required', 'exists:unidades,id'],
            'stock_actual' => ['required', 'numeric', 'min:0'],
            'stock_minimo' => ['required', 'numeric', 'min:0'],
            'costo' => ['required', 'numeric', 'min:0'],
            'precio_venta' => ['required', 'numeric', 'min:0'],
            'materias_config' => ['nullable', 'array'],
            'materias_items' => ['nullable', 'array'],
            'materias_items.*.materia_prima_id' => ['required', 'exists:materias_primas,id'],
        ]);

        $materiasConfig = $validated['materias_config'] ?? [];
        $materiasItems = $validated['materias_items'] ?? [];

        // Calcular consumo por formula para cada MP
        $materiasConConsumo = [];
        foreach ($materiasItems as $item) {
            $mp = MateriaPrima::find($item['materia_prima_id']);
            if (!$mp || !$mp->formula_consumo) {
                return back()->withInput()->withErrors([
                    'materias_items' => "La materia prima \"{$mp->nombre}\" no tiene formula de consumo definida.",
                ]);
            }

            $configMP = $materiasConfig[$mp->id] ?? [];
            try {
                $consumo = FormulaEvaluator::calcularConsumo(
                    $mp->formula_consumo,
                    $configMP,
                    $mp->campos_valores ?? []
                );
            } catch (\InvalidArgumentException $e) {
                return back()->withInput()->withErrors([
                    'materias_items' => "Error en formula de \"{$mp->nombre}\": {$e->getMessage()}",
                ]);
            }

            $materiasConConsumo[] = [
                'materia_prima_id' => $mp->id,
                'consumo_por_unidad' => $consumo,
            ];
        }

        $product = Product::create([
            'name' => $validated['name'],
            'descripcion' => $validated['descripcion'] ?? null,
            'product_type_id' => $validated['product_type_id'],
            'proveedor_id' => $validated['proveedor_id'],
            'unidad_id' => $validated['unidad_id'],
            'stock_actual' => $validated['stock_actual'],
            'stock_minimo' => $validated['stock_minimo'],
            'materias_config' => !empty($materiasConfig) ? $materiasConfig : null,
            'created_by' => auth()->id(),
        ]);

        ProductPrice::create([
            'product_id' => $product->id,
            'costo' => $validated['costo'],
            'precio_venta' => $validated['precio_venta'],
            'vigente_desde' => now(),
            'created_by' => auth()->id(),
        ]);

        foreach ($materiasConConsumo as $item) {
            ProductoMateriaPrima::create([
                'product_id' => $product->id,
                'materia_prima_id' => $item['materia_prima_id'],
                'consumo_por_unidad' => $item['consumo_por_unidad'],
            ]);
        }

        return redirect()->route('products.index')
            ->with('success', 'Producto creado exitosamente.');
    }

    public function show(Product $product)
    {
        $product->load(['prices', 'stockMovements', 'productoMateriasPrimas.materiaPrima']);
        return view('products.show', compact('product'));
    }

    public function edit(Product $product)
    {
        $proveedores = Proveedor::where('eliminado', 0)->orderBy('nombre')->get();
        $productTypes = ProductType::where('eliminado', 0)->orderBy('nombre')->get();
        $unidades = Unidad::where('eliminado', 0)->orderBy('descripcion')->get();
        $materiasPrimas = MateriaPrima::where('eliminado', 0)->orderBy('nombre')->get();
        $product->load('productoMateriasPrimas.materiaPrima');

        return view('products.edit', compact('product', 'proveedores', 'productTypes', 'unidades', 'materiasPrimas'));
    }

    public function update(Request $request, Product $product)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'descripcion' => ['nullable', 'string'],
            'product_type_id' => ['required', 'exists:product_types,id'],
            'proveedor_id' => ['required', 'exists:proveedores,id'],
            'unidad_id' => ['required', 'exists:unidades,id'],
            'stock_actual' => ['required', 'numeric', 'min:0'],
            'stock_minimo' => ['required', 'numeric', 'min:0'],
            'costo' => ['required', 'numeric', 'min:0'],
            'precio_venta' => ['required', 'numeric', 'min:0'],
            'materias_config' => ['nullable', 'array'],
            'materias_items' => ['nullable', 'array'],
            'materias_items.*.materia_prima_id' => ['required', 'exists:materias_primas,id'],
        ]);

        $materiasConfig = $validated['materias_config'] ?? [];
        $materiasItems = $validated['materias_items'] ?? [];

        // Calcular consumo por formula para cada MP
        $materiasConConsumo = [];
        foreach ($materiasItems as $item) {
            $mp = MateriaPrima::find($item['materia_prima_id']);
            if (!$mp || !$mp->formula_consumo) {
                return back()->withInput()->withErrors([
                    'materias_items' => "La materia prima \"{$mp->nombre}\" no tiene formula de consumo definida.",
                ]);
            }

            $configMP = $materiasConfig[$mp->id] ?? [];
            try {
                $consumo = FormulaEvaluator::calcularConsumo(
                    $mp->formula_consumo,
                    $configMP,
                    $mp->campos_valores ?? []
                );
            } catch (\InvalidArgumentException $e) {
                return back()->withInput()->withErrors([
                    'materias_items' => "Error en formula de \"{$mp->nombre}\": {$e->getMessage()}",
                ]);
            }

            $materiasConConsumo[] = [
                'materia_prima_id' => $mp->id,
                'consumo_por_unidad' => $consumo,
            ];
        }

        $product->update([
            'name' => $validated['name'],
            'descripcion' => $validated['descripcion'] ?? null,
            'product_type_id' => $validated['product_type_id'],
            'proveedor_id' => $validated['proveedor_id'],
            'unidad_id' => $validated['unidad_id'],
            'stock_actual' => $validated['stock_actual'],
            'stock_minimo' => $validated['stock_minimo'],
            'materias_config' => !empty($materiasConfig) ? $materiasConfig : null,
        ]);

        $currentPrice = $product->currentPrice();
        if (!$currentPrice ||
            $currentPrice->costo != $validated['costo'] ||
            $currentPrice->precio_venta != $validated['precio_venta']) {

            ProductPrice::create([
                'product_id' => $product->id,
                'costo' => $validated['costo'],
                'precio_venta' => $validated['precio_venta'],
                'vigente_desde' => now(),
                'created_by' => auth()->id(),
            ]);
        }

        $product->productoMateriasPrimas()->delete();
        foreach ($materiasConConsumo as $item) {
            ProductoMateriaPrima::create([
                'product_id' => $product->id,
                'materia_prima_id' => $item['materia_prima_id'],
                'consumo_por_unidad' => $item['consumo_por_unidad'],
            ]);
        }

        return redirect()->route('products.index')
            ->with('success', 'Producto actualizado exitosamente.');
    }

    public function destroy(Product $product)
    {
        $product->update(['eliminado' => 1]);

        return redirect()->route('products.index')
            ->with('success', 'Producto eliminado exitosamente.');
    }
}
