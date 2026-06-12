<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',           // nombre(s)
        'apellidos',
        'matricula',
        'carrera',
        'role',
        'semestre',
        'email',
        'password',
        'estatus_servicio_social',
        'turno_id',
        'grupo',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    // Relación con Turno
    public function turno()
    {
        return $this->belongsTo(Turno::class);
    }

    // Accesor para obtener el nombre del turno fácilmente
    public function getNombreTurnoAttribute()
    {
        return $this->turno ? $this->turno->nombre : 'No definido';
    }

    // Accesor para obtener el nombre del periodo actual
    public function getNombrePeriodoActualAttribute()
    {
        $periodo = $this->periodoActual();
        return $periodo ? $periodo->nombre : 'No definido';
    }

    // Relación uno a uno con ServicioSocial
    public function servicioSocial()
    {
        return $this->hasOne(ServicioSocial::class);
    }

    // Relacion uno a uno con Practicas
    public function practicas()
    {
        return $this->hasOne(Practica::class);
    }

    // Relacion uno a muchos con documentos
    public function documentos()
    {
        return $this->hasMany(Documento::class);
    }

    // Relacion uno a muchos con cartasPresentacion
    public function cartasPresentacion()
    {
        return $this->hasMany(CartaPresentacion::class);
    }

    // Relación muchos a muchos con periodos (historial)
    public function periodos()
    {
        return $this->belongsToMany(Periodo::class, 'estudiante_periodo')
                    ->withPivot('estatus')
                    ->withTimestamps();
    }

    // Periodo actual del estudiante
    public function periodoActual()
    {
        return $this->belongsToMany(Periodo::class, 'estudiante_periodo')
                    ->wherePivot('estatus', 'cursando')
                    ->withPivot('estatus')
                    ->first();
    }
}