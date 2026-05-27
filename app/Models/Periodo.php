<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Periodo extends Model
{
    use HasFactory;

    protected $fillable = ['nombre', 'fecha_inicio', 'fecha_fin', 'activo'];

    public function estudiantes()
    {
        return $this->belongsToMany(User::class, 'estudiante_periodo')
                    ->withPivot('activo', 'fecha_asignacion')
                    ->withTimestamps();
    }
}