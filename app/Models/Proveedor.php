<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Proveedor extends Model
{
    use HasFactory;

    protected $table = 'proveedores';

    protected $fillable = [
        'nombre',
        'descripcion',
        'created_by',
        'fecha_creacion',
        'eliminado',
    ];

    protected $casts = [
        'fecha_creacion' => 'datetime',
    ];

    /**
     * Relación: Proveedor tiene muchos Productos
     */
    public function products(): HasMany
    {
        return $this->hasMany(Product::class, 'proveedor_id');
    }

    /**
     * Relación: Proveedor creado por un Usuario
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
