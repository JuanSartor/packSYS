<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class PaperCoil extends Model
{
    use HasFactory;

    protected $fillable = [
        'tipo_papel',
        'ancho',
        'gramaje',
        'peso_inicial',
        'peso_actual',
        'alerta_minima',
    ];

    protected $casts = [
        'ancho' => 'decimal:2',
        'gramaje' => 'decimal:2',
        'peso_inicial' => 'decimal:2',
        'peso_actual' => 'decimal:2',
        'alerta_minima' => 'decimal:2',
    ];

    /**
     * Relación: Bobina tiene muchos Materiales de Producto
     */
    public function materials(): HasMany
    {
        return $this->hasMany(ProductMaterial::class);
    }

    /**
     * Relación: Bobina tiene muchos Movimientos de Stock
     */
    public function stockMovements(): HasMany
    {
        return $this->hasMany(StockMovement::class);
    }

    /**
     * Relación: Bobina pertenece a muchos Productos (a través de product_materials)
     */
    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'product_materials')
            ->withPivot('consumo_por_unidad');
    }
}
