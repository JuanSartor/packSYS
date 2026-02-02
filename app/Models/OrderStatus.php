<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderStatus extends Model
{
    use HasFactory;

    protected $table = 'order_status';

    protected $fillable = [
        'nombre',
        'descripcion',
        'created_by',
        'eliminado',
    ];

    /**
     * Relación: Estado creado por un Usuario
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
