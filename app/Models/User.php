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
        return $this->hasMany(Documentos::class);
    }

    // Relacion uno a muchos con cartasPresentacion
    public function cartasPresentacion()
    {
        return $this->hasMany(CartaPresentacion::class);
    }

    // Relacion muchos a muchos con periodos
    public function periodos()
    {
        return $this->belongsToMany(Periodo::class, 'estudiante_periodo')
            ->withPivot('activo', 'fecha_asignacion')
            ->withTimestamps();
    }
}
