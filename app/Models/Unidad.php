<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Unidad extends Model
{
    use HasFactory;

    protected $table = 'unidades';

    protected $fillable = [
        'descripcion',
        'created_by',
        'eliminado',
    ];

    /**
     * Relación: Unidad tiene muchos Productos
     */
    public function products(): HasMany
    {
        return $this->hasMany(Product::class, 'unidad_id');
    }

    /**
     * Relación: Unidad creada por un Usuario
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
