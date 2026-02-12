<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductPrice;
use App\Models\ProductType;
use App\Models\Proveedor;
use App\Models\Unidad;
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

        // Si viene un ID de producto similar, cargar sus datos
        $similarProduct = null;
        if (request('similar')) {
            $similarProduct = Product::with('prices')->find(request('similar'));
        }

        return view('products.create', compact('proveedores', 'productTypes', 'unidades', 'similarProduct'));
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
            'usa_bobina' => ['boolean'],
            'ancho' => ['required_if:usa_bobina,1', 'nullable', 'numeric', 'min:0'],
            'largo' => ['required_if:usa_bobina,1', 'nullable', 'numeric', 'min:0'],
            'fuelle' => ['required_if:usa_bobina,1', 'nullable', 'numeric', 'min:0'],
            'costo' => ['required', 'numeric', 'min:0'],
            'precio_venta' => ['required', 'numeric', 'min:0'],
        ]);

        $product = Product::create(array_merge($validated, [
            'created_by' => auth()->id(),
        ]));

        // Crear el primer registro de precio
        ProductPrice::create([
            'product_id' => $product->id,
            'costo' => $validated['costo'],
            'precio_venta' => $validated['precio_venta'],
            'vigente_desde' => now(),
            'created_by' => auth()->id(),
        ]);

        return redirect()->route('products.index')
            ->with('success', 'Producto creado exitosamente.');
    }

    public function show(Product $product)
    {
        $product->load(['prices', 'stockMovements']);
        return view('products.show', compact('product'));
    }

    public function edit(Product $product)
    {
        $proveedores = Proveedor::where('eliminado', 0)->orderBy('nombre')->get();
        $productTypes = ProductType::where('eliminado', 0)->orderBy('nombre')->get();
        $unidades = Unidad::where('eliminado', 0)->orderBy('descripcion')->get();
        return view('products.edit', compact('product', 'proveedores', 'productTypes', 'unidades'));
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
            'usa_bobina' => ['nullable', 'boolean'],
            'ancho' => ['nullable', 'numeric', 'min:0'],
            'largo' => ['nullable', 'numeric', 'min:0'],
            'fuelle' => ['nullable', 'numeric', 'min:0'],
            'costo' => ['required', 'numeric', 'min:0'],
            'precio_venta' => ['required', 'numeric', 'min:0'],
        ]);

        $product->update($validated);

        // Verificar si el precio cambió
        $currentPrice = $product->currentPrice();
        if (!$currentPrice ||
            $currentPrice->costo != $validated['costo'] ||
            $currentPrice->precio_venta != $validated['precio_venta']) {

            // Crear nuevo registro de precio
            ProductPrice::create([
                'product_id' => $product->id,
                'costo' => $validated['costo'],
                'precio_venta' => $validated['precio_venta'],
                'vigente_desde' => now(),
                'created_by' => auth()->id(),
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
