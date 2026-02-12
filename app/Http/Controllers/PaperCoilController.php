<?php

namespace App\Http\Controllers;

use App\Models\PaperCoil;
use Illuminate\Http\Request;

class PaperCoilController extends Controller
{
    public function index()
    {
        $search = request('search');

        $paperCoils = PaperCoil::where('eliminado', 0)
            ->when($search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('tipo_papel', 'like', "%{$search}%")
                      ->orWhere('proveedor', 'like', "%{$search}%");
                });
            })
            ->latest('id')
            ->paginate(15)
            ->appends(['search' => $search]);

        return view('paper-coils.index', compact('paperCoils'));
    }

    public function create()
    {
        return view('paper-coils.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'tipo_papel' => ['required', 'string', 'max:50'],
            'ancho' => ['required', 'numeric', 'min:0'],
            'gramaje' => ['required', 'numeric', 'min:0'],
            'peso_inicial' => ['required', 'numeric', 'min:0'],
            'peso_actual' => ['required', 'numeric', 'min:0'],
            'alerta_minima' => ['required', 'numeric', 'min:0'],
        ]);

        PaperCoil::create($validated);

        return redirect()->route('paper-coils.index')
            ->with('success', 'Bobina de papel creada exitosamente.');
    }

    public function show(PaperCoil $paperCoil)
    {
        $paperCoil->load('products');
        return view('paper-coils.show', compact('paperCoil'));
    }

    public function edit(PaperCoil $paperCoil)
    {
        return view('paper-coils.edit', compact('paperCoil'));
    }

    public function update(Request $request, PaperCoil $paperCoil)
    {
        $validated = $request->validate([
            'tipo_papel' => ['required', 'string', 'max:50'],
            'ancho' => ['required', 'numeric', 'min:0'],
            'gramaje' => ['required', 'numeric', 'min:0'],
            'peso_inicial' => ['required', 'numeric', 'min:0'],
            'peso_actual' => ['required', 'numeric', 'min:0'],
            'alerta_minima' => ['required', 'numeric', 'min:0'],
        ]);

        $paperCoil->update($validated);

        return redirect()->route('paper-coils.index')
            ->with('success', 'Bobina de papel actualizada exitosamente.');
    }

    public function destroy(PaperCoil $paperCoil)
    {
        $paperCoil->update(['eliminado' => 1]);

        return redirect()->route('paper-coils.index')
            ->with('success', 'Bobina de papel eliminada exitosamente.');
    }
}
