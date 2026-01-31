<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Canal extends Model
{
    use HasFactory;

    protected $table = 'canales';

    protected $fillable = [
        'descripcion',
        'eliminado',
    ];

    public function clients()
    {
        return $this->hasMany(Client::class);
    }
}
