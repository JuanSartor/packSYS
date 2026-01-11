<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Client extends Model
{
    use HasFactory;

    protected $fillable = [
        'nombre',
        'telefono',
        'email',
        'direccion',
    ];

    /**
     * Relación: Cliente tiene muchas Ventas
     */
    public function sales(): HasMany
    {
        return $this->hasMany(Sale::class);
    }
}
