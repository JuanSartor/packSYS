<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductPrice extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'product_id',
        'costo',
        'precio_venta',
        'vigente_desde',
    ];

    protected $casts = [
        'costo' => 'decimal:2',
        'precio_venta' => 'decimal:2',
        'vigente_desde' => 'datetime',
    ];

    /**
     * Relación: Precio pertenece a un Producto
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
