<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Empresa extends Model
{
    use HasFactory;

    protected $fillable = [
        'nombre',
        'direccion',
        'telefono',
        'contacto',
        'activo',
        'servicio_social',
        'practicas',
        'programa_dual',
        'fecha_termino_convenio',
    ];

    protected $casts = [
        'activo' => 'boolean',
        'servicio_social' => 'boolean',
        'practicas' => 'boolean',
        'programa_dual' => 'boolean',
        'fecha_termino_convenio' => 'date',
    ];

    public function scopeConServicioSocial($query)
    {
        return $query->where('servicio_social', true);
    }

    public function scopeConPracticas($query)
    {
        return $query->where('practicas', true);
    }

    public function scopeVigentes($query)
    {
        return $query->where('activo', true)
            ->where(function($q) {
                $q->whereNull('fecha_termino_convenio')
                  ->orWhere('fecha_termino_convenio', '>=', now());
            });
    }

    public function servicioSocial()
    {
        return $this->hasMany(ServicioSocial::class);
    }

    public function practicas()
    {
        return $this->hasMany(Practica::class);
    }
}