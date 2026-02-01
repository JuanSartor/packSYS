<?php

namespace App\Http\Controllers;

use App\Models\Proveedor;
use Illuminate\Http\Request;

class ProveedorController extends Controller
{
    public function index()
    {
        $proveedores = Proveedor::where('eliminado', 0)->latest()->paginate(15);
        return view('proveedores.index', compact('proveedores'));
    }

    public function create()
    {
        return view('proveedores.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nombre' => ['required', 'string', 'max:150'],
            'descripcion' => ['nullable', 'string'],
        ]);

        Proveedor::create(array_merge($validated, [
            'created_by' => auth()->id(),
            'fecha_creacion' => now(),
        ]));

        return redirect()->route('proveedores.index')
            ->with('success', 'Proveedor creado exitosamente.');
    }

    public function show(Proveedor $proveedor)
    {
        $proveedor->load('products');
        return view('proveedores.show', compact('proveedor'));
    }

    public function edit(Proveedor $proveedor)
    {
        return view('proveedores.edit', compact('proveedor'));
    }

    public function update(Request $request, Proveedor $proveedor)
    {
        $validated = $request->validate([
            'nombre' => ['required', 'string', 'max:150'],
            'descripcion' => ['nullable', 'string'],
        ]);

        $proveedor->update($validated);

        return redirect()->route('proveedores.index')
            ->with('success', 'Proveedor actualizado exitosamente.');
    }

    public function destroy(Proveedor $proveedor)
    {
        $proveedor->update(['eliminado' => 1]);

        return redirect()->route('proveedores.index')
            ->with('success', 'Proveedor eliminado exitosamente.');
    }
}
