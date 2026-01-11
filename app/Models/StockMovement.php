<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StockMovement extends Model
{
    use HasFactory;

    const UPDATED_AT = null;

    protected $fillable = [
        'product_id',
        'paper_coil_id',
        'tipo',
        'cantidad',
        'referencia',
        'referencia_id',
    ];

    protected $casts = [
        'cantidad' => 'decimal:2',
    ];

    /**
     * Relación: Movimiento pertenece a un Producto
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Relación: Movimiento pertenece a una Bobina de Papel
     */
    public function paperCoil(): BelongsTo
    {
        return $this->belongsTo(PaperCoil::class);
    }
}
