<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Turno extends Model
{
    use HasFactory;

    protected $fillable = [
        'nombre',
    ];

    // Relación: un turno tiene muchos horarios
    public function horarios()
    {
        return $this->hasMany(Horario::class);
    }

    // Relación: un turno tiene muchos usuarios
    public function users()
    {
        return $this->hasMany(User::class);
    }
}