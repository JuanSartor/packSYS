<?php

namespace App\Http\Controllers;

use App\Models\OrderStatus;
use Illuminate\Http\Request;

class OrderStatusController extends Controller
{
    public function index()
    {
        $orderStatuses = OrderStatus::where('eliminado', 0)->latest()->paginate(15);
        return view('order-statuses.index', compact('orderStatuses'));
    }

    public function create()
    {
        return view('order-statuses.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nombre' => ['required', 'string', 'max:100'],
            'descripcion' => ['nullable', 'string', 'max:255'],
        ]);

        $orderStatus = OrderStatus::create(array_merge($validated, [
            'created_by' => auth()->id(),
        ]));

        // Si es una petición AJAX, devolver JSON
        if ($request->expectsJson()) {
            return response()->json([
                'id' => $orderStatus->id,
                'nombre' => $orderStatus->nombre,
                'descripcion' => $orderStatus->descripcion,
            ]);
        }

        return redirect()->route('order-statuses.index')
            ->with('success', 'Estado de orden creado exitosamente.');
    }

    public function edit(OrderStatus $orderStatus)
    {
        return view('order-statuses.edit', compact('orderStatus'));
    }

    public function update(Request $request, OrderStatus $orderStatus)
    {
        $validated = $request->validate([
            'nombre' => ['required', 'string', 'max:100'],
            'descripcion' => ['nullable', 'string', 'max:255'],
        ]);

        $orderStatus->update($validated);

        return redirect()->route('order-statuses.index')
            ->with('success', 'Estado de orden actualizado exitosamente.');
    }

    public function destroy(OrderStatus $orderStatus)
    {
        $orderStatus->update(['eliminado' => 1]);

        return redirect()->route('order-statuses.index')
            ->with('success', 'Estado de orden eliminado exitosamente.');
    }
}
