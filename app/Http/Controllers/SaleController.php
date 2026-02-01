<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Product;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\StockMovement;
use App\Models\Transport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SaleController extends Controller
{
    public function index()
    {
        $sales = Sale::where('eliminado', 0)
            ->with(['client', 'creator', 'transport'])
            ->latest('id')
            ->paginate(15);

        return view('sales.index', compact('sales'));
    }

    public function create()
    {
        $clients = Client::where('eliminado', 0)->orderBy('nombre')->get();
        $products = Product::where('eliminado', 0)->where('stock_actual', '>', 0)->orderBy('name')->get();
        $transports = Transport::where('eliminado', 0)->orderBy('nombre')->get();

        // Preparar datos de productos para JavaScript
        $productsData = $products->map(function($p) {
            $currentPrice = $p->currentPrice();
            return [
                'id' => $p->id,
                'name' => $p->name,
                'price' => $currentPrice ? $currentPrice->precio_venta : 0,
                'price_id' => $currentPrice ? $currentPrice->id : null,
                'stock' => $p->stock_actual
            ];
        });

        return view('sales.create', compact('clients', 'products', 'transports', 'productsData'));
    }

    public function store(Request $request)
    {
        DB::beginTransaction();

        try {
            // Validar datos básicos
            $validated = $request->validate([
                'client_id' => ['required', 'exists:clients,id'],
                'transport_id' => ['required', 'exists:transports,id'],
                'items' => ['required', 'array', 'min:1'],
                'items.*.product_id' => ['required', 'exists:products,id'],
                'items.*.product_price_id' => ['nullable', 'exists:product_prices,id'],
                'items.*.cantidad' => ['required', 'numeric', 'min:0.01'],
                'items.*.precio_unitario' => ['required', 'numeric', 'min:0'],
            ]);

            // Verificar stock suficiente para cada producto
            foreach ($validated['items'] as $item) {
                $product = Product::find($item['product_id']);

                if ($product->stock_actual < $item['cantidad']) {
                    throw new \Exception("Stock insuficiente para {$product->name}. Disponible: {$product->stock_actual}, Solicitado: {$item['cantidad']}");
                }
            }

            // Calcular total
            $total = collect($validated['items'])->sum(function ($item) {
                return $item['cantidad'] * $item['precio_unitario'];
            });

            // Agregar costo de transporte si existe
            if ($validated['transport_id']) {
                $transport = Transport::find($validated['transport_id']);
                $total += $transport->costo;
            }

            // Crear la venta
            $sale = Sale::create([
                'client_id' => $validated['client_id'],
                'transport_id' => $validated['transport_id'],
                'total' => $total,
                'created_by' => auth()->id(),
            ]);

            // Crear items de venta y actualizar stock
            foreach ($validated['items'] as $item) {
                // Crear item de venta
                SaleItem::create([
                    'sale_id' => $sale->id,
                    'product_id' => $item['product_id'],
                    'product_price_id' => $item['product_price_id'] ?? null,
                    'cantidad' => $item['cantidad'],
                    'precio_unitario_venta' => $item['precio_unitario'],
                ]);

                // Restar stock del producto
                $product = Product::find($item['product_id']);
                $product->decrement('stock_actual', $item['cantidad']);

                // Crear movimiento de stock
                StockMovement::create([
                    'product_id' => $item['product_id'],
                    'tipo' => 'salida',
                    'cantidad' => $item['cantidad'],
                    'descripcion' => "Venta #{$sale->id} - Cliente: {$sale->client->nombre}",
                ]);
            }

            DB::commit();

            return redirect()->route('sales.index')
                ->with('success', 'Venta creada exitosamente.');

        } catch (\Exception $e) {
            DB::rollBack();

            return back()
                ->withInput()
                ->with('error', $e->getMessage());
        }
    }

    public function show(Sale $sale)
    {
        $sale->load(['client', 'creator', 'transport', 'items.product']);
        return view('sales.show', compact('sale'));
    }

    public function edit(Sale $sale)
    {
        // No se permite editar ventas una vez creadas
        return redirect()->route('sales.index')
            ->with('error', 'No se pueden editar ventas ya registradas.');
    }

    public function update(Request $request, Sale $sale)
    {
        // No se permite actualizar ventas
        return redirect()->route('sales.index')
            ->with('error', 'No se pueden editar ventas ya registradas.');
    }

    public function destroy(Sale $sale)
    {
        $sale->update(['eliminado' => 1]);

        return redirect()->route('sales.index')
            ->with('success', 'Venta eliminada exitosamente.');
    }
}
