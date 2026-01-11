<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'type',
        'unidad',
        'stock_actual',
        'stock_minimo',
        'usa_bobina',
    ];

    protected $casts = [
        'stock_actual' => 'decimal:2',
        'stock_minimo' => 'decimal:2',
        'usa_bobina' => 'boolean',
    ];

    /**
     * Relación: Producto tiene muchas Órdenes de Producción
     */
    public function productionOrders(): HasMany
    {
        return $this->hasMany(ProductionOrder::class);
    }

    /**
     * Relación: Producto tiene muchos Movimientos de Stock
     */
    public function stockMovements(): HasMany
    {
        return $this->hasMany(StockMovement::class);
    }

    /**
     * Relación: Producto tiene muchos Items de Venta
     */
    public function saleItems(): HasMany
    {
        return $this->hasMany(SaleItem::class);
    }

    /**
     * Relación: Producto tiene muchos Precios
     */
    public function prices(): HasMany
    {
        return $this->hasMany(ProductPrice::class);
    }

    /**
     * Relación: Producto tiene muchos Materiales (Bobinas)
     */
    public function materials(): HasMany
    {
        return $this->hasMany(ProductMaterial::class);
    }

    /**
     * Relación: Producto pertenece a muchas Bobinas de Papel (a través de product_materials)
     */
    public function paperCoils(): BelongsToMany
    {
        return $this->belongsToMany(PaperCoil::class, 'product_materials')
            ->withPivot('consumo_por_unidad');
    }

    /**
     * Obtener precio actual del producto
     */
    public function currentPrice()
    {
        return $this->prices()->latest('vigente_desde')->first();
    }
}
