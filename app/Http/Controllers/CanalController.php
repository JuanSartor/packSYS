<?php

namespace App\Http\Controllers;

use App\Models\Canal;
use Illuminate\Http\Request;

class CanalController extends Controller
{
    public function index()
    {
        $canales = Canal::where('eliminado', 0)->latest()->paginate(15);
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

        Canal::create($validated);

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
