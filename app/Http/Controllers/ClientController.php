<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Canal;
use Illuminate\Http\Request;

class ClientController extends Controller
{
    public function index()
    {
        $clients = Client::where('eliminado', 0)->latest()->paginate(15);
        return view('clients.index', compact('clients'));
    }

    public function create()
    {
        $canales = Canal::where('eliminado', 0)->get();
        return view('clients.create', compact('canales'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nombre' => ['required', 'string', 'max:150'],
            'telefono' => ['nullable', 'string', 'max:50'],
            'email' => ['nullable', 'email', 'max:150'],
            'direccion' => ['nullable', 'string', 'max:255'],
            'canal_id' => ['required', 'exists:canales,id'],
        ]);

        Client::create($validated);

        return redirect()->route('clients.index')
            ->with('success', 'Cliente creado exitosamente.');
    }

    public function show(Client $client)
    {
        $client->load('sales');
        return view('clients.show', compact('client'));
    }

    public function edit(Client $client)
    {
        $canales = Canal::where('eliminado', 0)->get();
        return view('clients.edit', compact('client', 'canales'));
    }

    public function update(Request $request, Client $client)
    {
        $validated = $request->validate([
            'nombre' => ['required', 'string', 'max:150'],
            'telefono' => ['nullable', 'string', 'max:50'],
            'email' => ['nullable', 'email', 'max:150'],
            'direccion' => ['nullable', 'string', 'max:255'],
            'canal_id' => ['required', 'exists:canales,id'],
        ]);

        $client->update($validated);

        return redirect()->route('clients.index')
            ->with('success', 'Cliente actualizado exitosamente.');
    }

    public function destroy(Client $client)
    {
        $client->update(['eliminado' => 1]);

        return redirect()->route('clients.index')
            ->with('success', 'Cliente eliminado exitosamente.');
    }
}
