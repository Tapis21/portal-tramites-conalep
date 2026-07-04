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
        'cargo_persona_carta',
        'nombre_jefe_inmediato',
        'cargo_jefe_inmediato',
        'area_asignada',
        'apoyo_estudiante',
        'fecha_inicio',
        'fecha_limite_primer_informe',
        'fecha_limite_segundo_informe',
        'estatus',
        'horario_id',
        'grado_academico_jefe_id'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function empresa()
    {
        return $this->belongsTo(Empresa::class);
    }

    public function gradoAcademico()
    {
        return $this->belongsTo(GradoAcademico::class);
    }

    public function gradoAcademicoJefe()
    {
        return $this->belongsTo(GradoAcademico::class, 'grado_academico_jefe_id');
    }

    public function horario()
    {
        return $this->belongsTo(Horario::class);
    }

    public function comentarios()
    {
        return $this->morphMany(Comentario::class, 'comentable');
    }

    public function documentosCompletos()
    {
        $documentosRequeridos = [
            'Solicitud de Servicio Social',
            'Elección de Modalidad',
            'Carta de Presentación de Servicio Social',
            'Carta de Aceptación',
            'Evaluación de Competencias del Desempeño',
            'Carta de Liberación de Servicio Social',
            'Primer Informe de Actividades Trimestral',
            'Segundo Informe de Actividades Trimestral'
        ];

        $subidos = Documento::where('user_id', $this->user_id)
            ->where('activo', true)
            ->whereHas('tipoDocumento', function($q) use ($documentosRequeridos) {
                $q->whereIn('nombre', $documentosRequeridos)
                  ->where('tramite', 'SS');
            })
            ->count();

        return $subidos === count($documentosRequeridos);
    }

    protected static function booted()
    {
        static::deleted(function ($servicioSocial) {
            $servicioSocial->user->update([
                'estatus_servicio_social' => 'no_solicitado'
            ]);
        });
    }
}