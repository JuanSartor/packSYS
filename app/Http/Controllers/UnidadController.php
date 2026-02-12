<?php

namespace App\Http\Controllers;

use App\Models\Unidad;
use Illuminate\Http\Request;

class UnidadController extends Controller
{
    public function index()
    {
        $search = request('search');

        $unidades = Unidad::where('eliminado', 0)
            ->when($search, function ($query, $search) {
                $query->where('descripcion', 'like', "%{$search}%");
            })
            ->latest()
            ->paginate(15)
            ->appends(['search' => $search]);

        return view('unidades.index', compact('unidades'));
    }

    public function create()
    {
        return view('unidades.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'descripcion' => ['required', 'string', 'max:150'],
        ]);

        $unidad = Unidad::create(array_merge($validated, [
            'created_by' => auth()->id(),
        ]));

        // Si es una petición AJAX, devolver JSON
        if ($request->expectsJson()) {
            return response()->json([
                'id' => $unidad->id,
                'descripcion' => $unidad->descripcion,
            ]);
        }

        return redirect()->route('unidades.index')
            ->with('success', 'Unidad creada exitosamente.');
    }

    public function show(Unidad $unidad)
    {
        $unidad->load('products');
        return view('unidades.show', compact('unidad'));
    }

    public function edit(Unidad $unidad)
    {
        return view('unidades.edit', compact('unidad'));
    }

    public function update(Request $request, Unidad $unidad)
    {
        $validated = $request->validate([
            'descripcion' => ['required', 'string', 'max:150'],
        ]);

        $unidad->update($validated);

        return redirect()->route('unidades.index')
            ->with('success', 'Unidad actualizada exitosamente.');
    }

    public function destroy(Unidad $unidad)
    {
        $unidad->update(['eliminado' => 1]);

        return redirect()->route('unidades.index')
            ->with('success', 'Unidad eliminada exitosamente.');
    }
}
