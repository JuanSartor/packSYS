<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Sale extends Model
{
    use HasFactory;

    protected $fillable = [
        'client_id',
        'total',
        'transporte',
        'created_by',
    ];

    protected $casts = [
        'total' => 'decimal:2',
        'transporte' => 'boolean',
    ];

    /**
     * Relación: Venta pertenece a un Cliente
     */
    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    /**
     * Relación: Venta fue creada por un Usuario
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Relación: Venta tiene muchos Items
     */
    public function items(): HasMany
    {
        return $this->hasMany(SaleItem::class);
    }
}
