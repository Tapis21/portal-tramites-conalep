<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Periodo extends Model
{
    use HasFactory;

    protected $fillable = [
        'año_inicio',
        'año_fin',
        'nombre',
        'activo',
    ];

    protected static function booted()
    {
        static::creating(function ($periodo) {
            $periodo->nombre = $periodo->año_inicio . ' - ' . $periodo->año_fin;
        });
    }

    public function estudiantes()
    {
        return $this->belongsToMany(User::class, 'estudiante_periodo')
                    ->withPivot('estatus')
                    ->withTimestamps();
    }
}