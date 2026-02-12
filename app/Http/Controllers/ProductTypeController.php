<?php

namespace App\Http\Controllers;

use App\Models\ProductType;
use Illuminate\Http\Request;

class ProductTypeController extends Controller
{
    public function index()
    {
        $search = request('search');

        $productTypes = ProductType::where('eliminado', 0)
            ->when($search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('nombre', 'like', "%{$search}%")
                      ->orWhere('descripcion', 'like', "%{$search}%");
                });
            })
            ->latest()
            ->paginate(15)
            ->appends(['search' => $search]);

        return view('product-types.index', compact('productTypes'));
    }

    public function create()
    {
        return view('product-types.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nombre' => ['required', 'string', 'max:150'],
            'descripcion' => ['nullable', 'string'],
        ]);

        $productType = ProductType::create(array_merge($validated, [
            'created_by' => auth()->id(),
        ]));

        // Si es una petición AJAX, devolver JSON
        if ($request->expectsJson()) {
            return response()->json([
                'id' => $productType->id,
                'nombre' => $productType->nombre,
                'descripcion' => $productType->descripcion,
            ]);
        }

        return redirect()->route('product-types.index')
            ->with('success', 'Tipo de producto creado exitosamente.');
    }

    public function show(ProductType $productType)
    {
        $productType->load('products');
        return view('product-types.show', compact('productType'));
    }

    public function edit(ProductType $productType)
    {
        return view('product-types.edit', compact('productType'));
    }

    public function update(Request $request, ProductType $productType)
    {
        $validated = $request->validate([
            'nombre' => ['required', 'string', 'max:150'],
            'descripcion' => ['nullable', 'string'],
        ]);

        $productType->update($validated);

        return redirect()->route('product-types.index')
            ->with('success', 'Tipo de producto actualizado exitosamente.');
    }

    public function destroy(ProductType $productType)
    {
        $productType->update(['eliminado' => 1]);

        return redirect()->route('product-types.index')
            ->with('success', 'Tipo de producto eliminado exitosamente.');
    }
}
