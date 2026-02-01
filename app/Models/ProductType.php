<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductType extends Model
{
    use HasFactory;

    protected $fillable = [
        'nombre',
        'descripcion',
        'created_by',
        'eliminado',
    ];

    /**
     * Relación: Tipo de Producto tiene muchos Productos
     */
    public function products(): HasMany
    {
        return $this->hasMany(Product::class, 'product_type_id');
    }

    /**
     * Relación: Tipo de Producto creado por un Usuario
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
