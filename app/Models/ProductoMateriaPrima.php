<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductoMateriaPrima extends Model
{
    use HasFactory;

    protected $table = 'producto_materia_prima';

    public $timestamps = false;

    protected $fillable = [
        'product_id',
        'materia_prima_id',
        'consumo_por_unidad',
    ];

    protected $casts = [
        'consumo_por_unidad' => 'decimal:4',
    ];

    /**
     * Relacion: Vinculo pertenece a un Producto
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Relacion: Vinculo pertenece a una Materia Prima
     */
    public function materiaPrima(): BelongsTo
    {
        return $this->belongsTo(MateriaPrima::class);
    }
}
