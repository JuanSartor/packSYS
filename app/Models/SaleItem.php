<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SaleItem extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'sale_id',
        'product_id',
        'product_price_id',
        'cantidad',
        'precio_unitario_venta',
    ];

    protected $casts = [
        'precio_unitario_venta' => 'decimal:2',
    ];

    /**
     * Relación: Item pertenece a una Venta
     */
    public function sale(): BelongsTo
    {
        return $this->belongsTo(Sale::class);
    }

    /**
     * Relación: Item pertenece a un Producto
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Relación: Item pertenece a un ProductPrice (precio histórico)
     */
    public function productPrice(): BelongsTo
    {
        return $this->belongsTo(ProductPrice::class);
    }
}
