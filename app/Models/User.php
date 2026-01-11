<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    const ROLE_OPERARIO = 'operario';
    const ROLE_VENDEDOR = 'vendedor';
    const ROLE_GESTOR = 'gestor';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    /**
     * Check if user is operario
     */
    public function isOperario(): bool
    {
        return $this->role === self::ROLE_OPERARIO;
    }

    /**
     * Check if user is vendedor
     */
    public function isVendedor(): bool
    {
        return $this->role === self::ROLE_VENDEDOR;
    }

    /**
     * Check if user is gestor
     */
    public function isGestor(): bool
    {
        return $this->role === self::ROLE_GESTOR;
    }

    /**
     * Scope query to only include users with specific role
     */
    public function scopeRole($query, string $role)
    {
        return $query->where('role', $role);
    }

    /**
     * Scope query to only include operarios
     */
    public function scopeOperarios($query)
    {
        return $query->where('role', self::ROLE_OPERARIO);
    }

    /**
     * Scope query to only include vendedores
     */
    public function scopeVendedores($query)
    {
        return $query->where('role', self::ROLE_VENDEDOR);
    }

    /**
     * Scope query to only include gestores
     */
    public function scopeGestores($query)
    {
        return $query->where('role', self::ROLE_GESTOR);
    }

    /**
     * Relación: Usuario creó muchas Órdenes de Producción
     */
    public function productionOrders(): HasMany
    {
        return $this->hasMany(ProductionOrder::class, 'created_by');
    }

    /**
     * Relación: Usuario creó muchas Ventas
     */
    public function sales(): HasMany
    {
        return $this->hasMany(Sale::class, 'created_by');
    }
}
