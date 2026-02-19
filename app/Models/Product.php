<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'descripcion',
        'product_type_id',
        'proveedor_id',
        'unidad_id',
        'stock_actual',
        'stock_minimo',
        'usa_bobina',
        'ancho',
        'largo',
        'fuelle',
        'materias_config',
        'created_by',
        'eliminado',
    ];

    protected $casts = [
        'stock_actual' => 'decimal:2',
        'stock_minimo' => 'decimal:2',
        'usa_bobina' => 'boolean',
        'ancho' => 'decimal:2',
        'largo' => 'decimal:2',
        'fuelle' => 'decimal:2',
        'materias_config' => 'array',
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

    /**
     * Relación: Producto creado por un Usuario
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Relación: Producto pertenece a un Proveedor
     */
    public function proveedor(): BelongsTo
    {
        return $this->belongsTo(Proveedor::class);
    }

    /**
     * Relación: Producto pertenece a un Tipo de Producto
     */
    public function productType(): BelongsTo
    {
        return $this->belongsTo(ProductType::class);
    }

    /**
     * Relación: Producto pertenece a una Unidad
     */
    public function unidad(): BelongsTo
    {
        return $this->belongsTo(Unidad::class);
    }

    /**
     * Relación: Producto pertenece a muchas Materias Primas (sistema genérico)
     */
    public function materiasPrimas(): BelongsToMany
    {
        return $this->belongsToMany(MateriaPrima::class, 'producto_materia_prima')
            ->withPivot('consumo_por_unidad');
    }

    /**
     * Relación: Producto tiene muchos vínculos de Materia Prima
     */
    public function productoMateriasPrimas(): HasMany
    {
        return $this->hasMany(ProductoMateriaPrima::class);
    }
}
