<?php

namespace App\Http\Controllers;

use App\Models\OrderStatus;
use App\Models\Product;
use App\Models\ProductionOrder;
use App\Models\StockMovement;
use App\Services\FormulaEvaluator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProductionOrderController extends Controller
{
    public function index()
    {
        $search = request('search');

        $orders = ProductionOrder::where('eliminado', 0)
            ->with(['product', 'creator', 'orderStatus'])
            ->when($search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('estado', 'like', "%{$search}%")
                      ->orWhereHas('product', fn($q) => $q->where('name', 'like', "%{$search}%"))
                      ->orWhereHas('orderStatus', fn($q) => $q->where('nombre', 'like', "%{$search}%"));
                });
            })
            ->latest('id')
            ->paginate(15)
            ->appends(['search' => $search]);

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

        $product = Product::with('productoMateriasPrimas.materiaPrima')->find($validated['product_id']);
        $cantidad = $validated['cantidad'];

        // Verificar stock de materias primas antes de crear la orden
        $materiasInsuficientes = [];
        $maxProducible = PHP_INT_MAX;

        foreach ($product->productoMateriasPrimas as $pmp) {
            $mp = $pmp->materiaPrima;

            // Re-evaluar formula para obtener consumo actualizado
            $consumoPorUnidad = $pmp->consumo_por_unidad;
            if ($mp->formula_consumo) {
                $configMP = ($product->materias_config[$mp->id] ?? []);
                try {
                    $consumoPorUnidad = FormulaEvaluator::calcularConsumo(
                        $mp->formula_consumo,
                        $configMP,
                        $mp->campos_valores ?? []
                    );
                } catch (\InvalidArgumentException $e) {
                    // Si la formula falla, usar el valor almacenado
                }
            }

            $consumoTotal = $consumoPorUnidad * $cantidad;
            $maxConEsta = $consumoPorUnidad > 0
                ? floor($mp->stock_actual / $consumoPorUnidad)
                : PHP_INT_MAX;

            if ($consumoTotal > $mp->stock_actual) {
                $materiasInsuficientes[] = [
                    'nombre' => $mp->nombre,
                    'unidad' => $mp->unidad_consumo,
                    'stock_actual' => $mp->stock_actual,
                    'consumo_necesario' => round($consumoTotal, 4),
                    'max_producible' => $maxConEsta,
                ];
            }

            $maxProducible = min($maxProducible, $maxConEsta);
        }

        if (!empty($materiasInsuficientes)) {
            $mensaje = 'Stock insuficiente de materias primas. ';
            foreach ($materiasInsuficientes as $mi) {
                $mensaje .= "{$mi['nombre']}: necesita {$mi['consumo_necesario']} {$mi['unidad']}, disponible {$mi['stock_actual']} {$mi['unidad']}. ";
            }
            $mensaje .= "Cantidad maxima producible: {$maxProducible} unidades.";

            return back()->withInput()->withErrors(['cantidad' => $mensaje]);
        }

        $validated['created_by'] = auth()->id();

        ProductionOrder::create($validated);

        return redirect()->route('production-orders.index')
            ->with('success', 'Orden de producción creada exitosamente.');
    }

    public function show(ProductionOrder $productionOrder)
    {
        $productionOrder->load(['product.productoMateriasPrimas.materiaPrima', 'creator', 'productionTimes', 'orderStatus']);
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
        if (!in_array($productionOrder->estado, ['produccion', 'pausada'])) {
            return redirect()->route('production-orders.show', $productionOrder)
                ->with('error', 'La orden no puede ser finalizada en su estado actual.');
        }

        $product = $productionOrder->product;
        $product->load('productoMateriasPrimas.materiaPrima');
        $cantidad = $productionOrder->cantidad;

        // Re-evaluar formulas y verificar stock
        $materiasInsuficientes = [];
        $maxProducible = PHP_INT_MAX;
        $consumosPorMP = [];

        foreach ($product->productoMateriasPrimas as $pmp) {
            $mp = $pmp->materiaPrima;

            // Re-evaluar formula para obtener consumo actualizado
            $consumoPorUnidad = $pmp->consumo_por_unidad;
            if ($mp->formula_consumo) {
                $configMP = ($product->materias_config[$mp->id] ?? []);
                try {
                    $consumoPorUnidad = FormulaEvaluator::calcularConsumo(
                        $mp->formula_consumo,
                        $configMP,
                        $mp->campos_valores ?? []
                    );
                } catch (\InvalidArgumentException $e) {
                    // Si la formula falla, usar el valor almacenado
                }
            }

            $consumoTotal = $consumoPorUnidad * $cantidad;
            $maxConEsta = $consumoPorUnidad > 0
                ? floor($mp->stock_actual / $consumoPorUnidad)
                : PHP_INT_MAX;

            $consumosPorMP[$pmp->id] = $consumoTotal;

            if ($consumoTotal > $mp->stock_actual) {
                $materiasInsuficientes[] = [
                    'nombre' => $mp->nombre,
                    'unidad' => $mp->unidad_consumo,
                    'stock_actual' => $mp->stock_actual,
                    'consumo_necesario' => round($consumoTotal, 4),
                    'max_producible' => $maxConEsta,
                ];
            }

            $maxProducible = min($maxProducible, $maxConEsta);
        }

        if (!empty($materiasInsuficientes)) {
            $mensaje = 'No se puede finalizar: stock insuficiente de materias primas. ';
            foreach ($materiasInsuficientes as $mi) {
                $mensaje .= "{$mi['nombre']}: necesita {$mi['consumo_necesario']} {$mi['unidad']}, disponible {$mi['stock_actual']} {$mi['unidad']}. ";
            }
            $mensaje .= "Cantidad maxima producible: {$maxProducible} unidades.";

            return redirect()->route('production-orders.show', $productionOrder)
                ->with('error', $mensaje);
        }

        DB::beginTransaction();
        try {
            // Finalizar orden
            $productionOrder->update([
                'estado' => 'finalizada',
                'finished_at' => now(),
            ]);

            // Incrementar stock del producto
            $product->increment('stock_actual', $cantidad);

            // Crear movimiento de stock del producto
            StockMovement::create([
                'product_id' => $product->id,
                'tipo' => 'entrada',
                'cantidad' => $cantidad,
                'referencia' => 'production_order',
                'referencia_id' => $productionOrder->id,
            ]);

            // Descontar materias primas con consumo re-evaluado
            foreach ($product->productoMateriasPrimas as $pmp) {
                $consumoTotal = $consumosPorMP[$pmp->id];
                $pmp->materiaPrima->decrement('stock_actual', $consumoTotal);

                StockMovement::create([
                    'materia_prima_id' => $pmp->materia_prima_id,
                    'tipo' => 'salida',
                    'cantidad' => $consumoTotal,
                    'referencia' => 'production_order',
                    'referencia_id' => $productionOrder->id,
                ]);
            }

            DB::commit();

            return redirect()->route('production-orders.show', $productionOrder)
                ->with('success', 'Orden finalizada exitosamente. Stock actualizado y materias primas descontadas.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('production-orders.show', $productionOrder)
                ->with('error', 'Error al finalizar la orden: ' . $e->getMessage());
        }
    }
}
