<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductPrice;
use App\Models\Proveedor;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::where('eliminado', 0)->latest()->paginate(15);
        return view('products.index', compact('products'));
    }

    public function create()
    {
        $proveedores = Proveedor::where('eliminado', 0)->orderBy('nombre')->get();
        return view('products.create', compact('proveedores'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'descripcion' => ['nullable', 'string'],
            'type' => ['required', Rule::in(['bolsa_papel', 'friselina', 'caja', 'insumo'])],
            'proveedor_id' => ['nullable', 'exists:proveedores,id'],
            'unidad' => ['required', Rule::in(['unidad', 'kg', 'metro'])],
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
        return view('products.edit', compact('product', 'proveedores'));
    }

    public function update(Request $request, Product $product)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'descripcion' => ['nullable', 'string'],
            'type' => ['required', Rule::in(['bolsa_papel', 'friselina', 'caja', 'insumo'])],
            'proveedor_id' => ['nullable', 'exists:proveedores,id'],
            'unidad' => ['required', Rule::in(['unidad', 'kg', 'metro'])],
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
