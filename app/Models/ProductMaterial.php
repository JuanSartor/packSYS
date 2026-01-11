<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductMaterial extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'product_id',
        'paper_coil_id',
        'consumo_por_unidad',
    ];

    protected $casts = [
        'consumo_por_unidad' => 'decimal:4',
    ];

    /**
     * Relación: Material pertenece a un Producto
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Relación: Material pertenece a una Bobina de Papel
     */
    public function paperCoil(): BelongsTo
    {
        return $this->belongsTo(PaperCoil::class);
    }
}
