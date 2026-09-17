<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ticket extends Model
{
    // Esto le da permiso a Laravel de escribir los datos del formulario en MySQL
    protected $fillable = [
        'titulo',
        'solicitante',
        'correo',
        'categoria',
        'prioridad',
        'estado',
        'descripcion'
    ];
}
