<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductionTime extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'production_order_id',
        'inicio',
        'fin',
    ];

    protected $casts = [
        'inicio' => 'datetime',
        'fin' => 'datetime',
    ];

    /**
     * Relación: Tiempo pertenece a una Orden de Producción
     */
    public function productionOrder(): BelongsTo
    {
        return $this->belongsTo(ProductionOrder::class);
    }
}
