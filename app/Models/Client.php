<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Client extends Model
{
    use HasFactory;

    protected $fillable = [
        'nombre',
        'telefono',
        'email',
        'direccion',
        'canal_id',
        'eliminado',
    ];

    /**
     * Relación: Cliente pertenece a un Canal
     */
    public function canal(): BelongsTo
    {
        return $this->belongsTo(Canal::class);
    }

    /**
     * Relación: Cliente tiene muchas Ventas
     */
    public function sales(): HasMany
    {
        return $this->hasMany(Sale::class);
    }
}
