<?php

namespace App\Http\Controllers;

use App\Models\Transport;
use Illuminate\Http\Request;

class TransportController extends Controller
{
    public function index()
    {
        $transports = Transport::latest('id')->paginate(15);
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

        Transport::create($validated);

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
        $transport->delete();

        return redirect()->route('transports.index')
            ->with('success', 'Transporte eliminado exitosamente.');
    }
}
