<?php

namespace App\Http\Controllers;

use App\Models\OrderStatus;
use App\Models\Product;
use App\Models\ProductionOrder;
use Illuminate\Http\Request;

class ProductionOrderController extends Controller
{
    public function index()
    {
        $orders = ProductionOrder::where('eliminado', 0)
            ->with(['product', 'creator', 'orderStatus'])
            ->latest('id')
            ->paginate(15);

        return view('production-orders.index', compact('orders'));
    }

    public function create()
    {
        $products = Product::where('eliminado', 0)->orderBy('name')->get();
        $orderStatuses = OrderStatus::where('eliminado', 0)->orderBy('nombre')->get();
        return view('production-orders.create', compact('products', 'orderStatuses'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'product_id' => ['required', 'exists:products,id'],
            'cantidad' => ['required', 'numeric', 'min:0.01'],
            'order_status_id' => ['required', 'exists:order_status,id'],
        ]);

        $validated['created_by'] = auth()->id();

        ProductionOrder::create($validated);

        return redirect()->route('production-orders.index')
            ->with('success', 'Orden de producción creada exitosamente.');
    }

    public function show(ProductionOrder $productionOrder)
    {
        $productionOrder->load(['product', 'creator', 'productionTimes', 'orderStatus']);
        return view('production-orders.show', compact('productionOrder'));
    }

    public function edit(ProductionOrder $productionOrder)
    {
        $products = Product::where('eliminado', 0)->orderBy('name')->get();
        $orderStatuses = OrderStatus::where('eliminado', 0)->orderBy('nombre')->get();
        return view('production-orders.edit', compact('productionOrder', 'products', 'orderStatuses'));
    }

    public function update(Request $request, ProductionOrder $productionOrder)
    {
        $validated = $request->validate([
            'product_id' => ['required', 'exists:products,id'],
            'cantidad' => ['required', 'numeric', 'min:0.01'],
            'order_status_id' => ['required', 'exists:order_status,id'],
        ]);

        $productionOrder->update($validated);

        return redirect()->route('production-orders.index')
            ->with('success', 'Orden de producción actualizada exitosamente.');
    }

    public function destroy(ProductionOrder $productionOrder)
    {
        $productionOrder->update(['eliminado' => 1]);

        return redirect()->route('production-orders.index')
            ->with('success', 'Orden de producción eliminada exitosamente.');
    }

    /**
     * Iniciar producción
     */
    public function start(ProductionOrder $productionOrder)
    {
        if ($productionOrder->estado !== 'produccion') {
            $productionOrder->update([
                'estado' => 'produccion',
                'started_at' => now(),
            ]);

            return redirect()->route('production-orders.show', $productionOrder)
                ->with('success', 'Orden iniciada exitosamente.');
        }

        return redirect()->route('production-orders.show', $productionOrder)
            ->with('error', 'La orden ya está en producción.');
    }

    /**
     * Pausar producción
     */
    public function pause(ProductionOrder $productionOrder)
    {
        if ($productionOrder->estado === 'produccion') {
            $productionOrder->update(['estado' => 'pausada']);

            return redirect()->route('production-orders.show', $productionOrder)
                ->with('success', 'Orden pausada exitosamente.');
        }

        return redirect()->route('production-orders.show', $productionOrder)
            ->with('error', 'La orden no está en producción.');
    }

    /**
     * Finalizar producción
     */
    public function finish(ProductionOrder $productionOrder)
    {
        if (in_array($productionOrder->estado, ['produccion', 'pausada'])) {
            $productionOrder->update([
                'estado' => 'finalizada',
                'finished_at' => now(),
            ]);

            // Incrementar stock del producto
            $productionOrder->product->increment('stock_actual', $productionOrder->cantidad);

            return redirect()->route('production-orders.show', $productionOrder)
                ->with('success', 'Orden finalizada exitosamente. Stock actualizado.');
        }

        return redirect()->route('production-orders.show', $productionOrder)
            ->with('error', 'La orden no puede ser finalizada en su estado actual.');
    }
}
