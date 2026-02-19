<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class MateriaPrima extends Model
{
    use HasFactory;

    protected $table = 'materias_primas';

    protected $fillable = [
        'nombre',
        'descripcion',
        'unidad_consumo',
        'campos_inventario',
        'campos_producto',
        'campos_valores',
        'formula_consumo',
        'stock_actual',
        'stock_inicial',
        'alerta_minima',
        'created_by',
        'eliminado',
    ];

    protected $casts = [
        'campos_inventario' => 'array',
        'campos_producto' => 'array',
        'campos_valores' => 'array',
        'stock_actual' => 'decimal:2',
        'stock_inicial' => 'decimal:2',
        'alerta_minima' => 'decimal:2',
        'eliminado' => 'boolean',
    ];

    /**
     * Relacion: Materia Prima pertenece a muchos Productos
     */
    public function productos(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'producto_materia_prima')
            ->withPivot('consumo_por_unidad');
    }

    /**
     * Relacion: Materia Prima tiene muchos Movimientos de Stock
     */
    public function stockMovements(): HasMany
    {
        return $this->hasMany(StockMovement::class, 'materia_prima_id');
    }

    /**
     * Relacion: Materia Prima creada por un Usuario
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Obtener el valor de un campo dinamico
     */
    public function getCampoValor(string $nombre)
    {
        return $this->campos_valores[$nombre] ?? null;
    }
}
