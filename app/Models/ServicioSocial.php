<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ServicioSocial extends Model
{
    use HasFactory;

    protected $table = 'servicio_social';


    protected $fillable = [
        'user_id',
        'reporte_parcial_subido',
        'reporte_parcial_validado',
        'reporte_final_subido',
        'reporte_final_validado',
        'archivo_parcial',
        'archivo_final',
        'estatus',
        'fecha_inicio',
        'fecha_limite_primer_informe',
        'fecha_limite_segundo_informe',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function comentarios()
    {
        return $this->morphMany(Comentario::class, 'comentable');
    }

    protected static function booted()
    {
        static::updated(function ($servicioSocial) {
            $servicioSocial->user->update([
                'estatus_servicio_social' => $servicioSocial->estatus
            ]);
        });

        static::created(function ($servicioSocial) {
            $servicioSocial->user->update([
                'estatus_servicio_social' => $servicioSocial->estatus
            ]);
        });

        static::deleted(function ($servicioSocial) {
            $servicioSocial->user->update([
                'estatus_servicio_social' => 'no_solicitado'
            ]);
        });

        // 👇 ESTO ES NUEVO (para cambios de estatus)
        static::updating(function ($servicioSocial) {
            if ($servicioSocial->isDirty('estatus')) {
                $servicioSocial->user()->update([
                    'estatus_servicio_social' => $servicioSocial->estatus
                ]);
            }
        });
    }
}