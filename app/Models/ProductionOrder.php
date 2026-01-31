<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ProductionOrder extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'cantidad',
        'estado',
        'created_by',
        'started_at',
        'finished_at',
        'eliminado',
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'finished_at' => 'datetime',
    ];

    /**
     * Relación: Orden pertenece a un Producto
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Relación: Orden fue creada por un Usuario
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Relación: Orden tiene muchos Tiempos de Producción
     */
    public function productionTimes(): HasMany
    {
        return $this->hasMany(ProductionTime::class);
    }
}
