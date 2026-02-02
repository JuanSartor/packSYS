<?php

namespace App\Http\Controllers;

use App\Models\Transport;
use Illuminate\Http\Request;

class TransportController extends Controller
{
    public function index()
    {
        $transports = Transport::where('eliminado', 0)->latest()->paginate(15);
        return view('transports.index', compact('transports'));
    }

    public function create()
    {
        return view('transports.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nombre' => ['required', 'string', 'max:100'],
            'costo' => ['required', 'numeric', 'min:0'],
        ]);

        $transport = Transport::create(array_merge($validated, [
            'created_by' => auth()->id(),
        ]));

        // Si es una petición AJAX, devolver JSON
        if ($request->expectsJson()) {
            return response()->json([
                'id' => $transport->id,
                'nombre' => $transport->nombre,
                'costo' => $transport->costo,
            ]);
        }

        return redirect()->route('transports.index')
            ->with('success', 'Transporte creado exitosamente.');
    }

    public function edit(Transport $transport)
    {
        return view('transports.edit', compact('transport'));
    }

    public function update(Request $request, Transport $transport)
    {
        $validated = $request->validate([
            'nombre' => ['required', 'string', 'max:100'],
            'costo' => ['required', 'numeric', 'min:0'],
        ]);

        $transport->update($validated);

        return redirect()->route('transports.index')
            ->with('success', 'Transporte actualizado exitosamente.');
    }

    public function destroy(Transport $transport)
    {
        $transport->update(['eliminado' => 1]);

        return redirect()->route('transports.index')
            ->with('success', 'Transporte eliminado exitosamente.');
    }
}
