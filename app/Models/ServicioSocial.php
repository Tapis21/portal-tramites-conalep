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
        'empresa_id',
        'grado_academico_id',
        'nombre_persona_carta',
        'area_asignada',
        'apoyo_estudiante',
        'fecha_inicio',
        'fecha_limite_primer_informe',
        'fecha_limite_segundo_informe',
        'reporte_parcial_subido',
        'reporte_parcial_validado',
        'reporte_final_subido',
        'reporte_final_validado',
        'archivo_parcial',
        'archivo_final',
        'estatus',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function comentarios()
    {
        return $this->morphMany(Comentario::class, 'comentable');
    }

    /**
     * Verifica si todos los documentos obligatorios están subidos
     */
    public function documentosCompletos()
    {
        // Documentos administrativos requeridos
        $documentosRequeridos = [
            'Solicitud de Servicio Social',
            'Elección de Modalidad',
            'Carta de Presentación de Servicio Social',
            'Carta de Aceptación',
            'Evaluación de Competencias del Desempeño',
            'Carta de Liberación de Servicio Social'
        ];

        $subidos = Documento::where('user_id', $this->user_id)
            ->where('activo', true)
            ->whereHas('tipoDocumento', function($q) use ($documentosRequeridos) {
                $q->whereIn('nombre', $documentosRequeridos);
            })
            ->count();

        // Informes requeridos
        $informesSubidos = $this->reporte_parcial_subido && $this->reporte_final_subido;

        return $subidos === count($documentosRequeridos) && $informesSubidos;
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

        // Para cambios de estatus
        static::updating(function ($servicioSocial) {
            if ($servicioSocial->isDirty('estatus')) {
                $servicioSocial->user()->update([
                    'estatus_servicio_social' => $servicioSocial->estatus
                ]);
            }
        });
    }
}