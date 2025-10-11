<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Orden extends Model
{
    use HasFactory;

    protected $table = 'ordenes';

    protected $fillable = [
        'nombre',
        'direccion',
        'telefono',
        'servicio',
        'entrega',
        'pago',
        'total'
    ];
}
