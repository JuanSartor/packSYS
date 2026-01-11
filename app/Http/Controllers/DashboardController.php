<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Client;
use App\Models\Sale;
use App\Models\ProductionOrder;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'total_products' => Product::count(),
            'low_stock_products' => Product::whereColumn('stock_actual', '<=', 'stock_minimo')->count(),
            'total_clients' => Client::count(),
            'pending_orders' => ProductionOrder::whereIn('estado', ['espera', 'pendiente'])->count(),
            'recent_sales' => Sale::latest()->take(5)->with(['client', 'creator'])->get(),
        ];

        return view('dashboard', compact('stats'));
    }
}
