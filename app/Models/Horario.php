<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Horario extends Model
{
    use HasFactory;

    protected $fillable = [
        'turno_id',
        'hora_inicio',
        'hora_fin',
    ];

    // Relación: un horario pertenece a un turno
    public function turno()
    {
        return $this->belongsTo(Turno::class);
    }

    // Relación: un horario puede estar en muchos servicios sociales
    public function serviciosSociales()
    {
        return $this->hasMany(ServicioSocial::class);
    }

    // Relación: un horario puede estar en muchas prácticas
    public function practicas()
    {
        return $this->hasMany(Practica::class);
    }
}