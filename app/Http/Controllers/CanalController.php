<?php

namespace App\Http\Controllers;

use App\Models\Canal;
use Illuminate\Http\Request;

class CanalController extends Controller
{
    public function index()
    {
        $search = request('search');

        $canales = Canal::where('eliminado', 0)
            ->when($search, function ($query, $search) {
                $query->where('descripcion', 'like', "%{$search}%");
            })
            ->latest()
            ->paginate(15)
            ->appends(['search' => $search]);

        return view('canales.index', compact('canales'));
    }

    public function create()
    {
        return view('canales.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'descripcion' => ['required', 'string', 'max:255'],
        ]);

        $canal = Canal::create($validated);

        // Si es una petición AJAX, devolver JSON
        if ($request->expectsJson()) {
            return response()->json([
                'id' => $canal->id,
                'descripcion' => $canal->descripcion,
            ]);
        }

        return redirect()->route('canales.index')
            ->with('success', 'Canal creado exitosamente.');
    }

    public function edit(Canal $canal)
    {
        return view('canales.edit', compact('canal'));
    }

    public function update(Request $request, Canal $canal)
    {
        $validated = $request->validate([
            'descripcion' => ['required', 'string', 'max:255'],
        ]);

        $canal->update($validated);

        return redirect()->route('canales.index')
            ->with('success', 'Canal actualizado exitosamente.');
    }

    public function destroy(Canal $canal)
    {
        $canal->update(['eliminado' => 1]);

        return redirect()->route('canales.index')
            ->with('success', 'Canal eliminado exitosamente.');
    }
}
