<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Practica extends Model
{
    use HasFactory;

    protected $table = 'practicas';

    protected $fillable = [
        'user_id',
        'empresa_id',
        'grado_academico_id',
        'nombre_persona_carta',
        'cargo_persona_carta',
        'nombre_jefe_inmediato',
        'cargo_jefe_inmediato',
        'grado_academico_jefe_id',
        'horario_id',
        'area_asignada',
        'apoyo_estudiante',
        'fecha_inicio',
        'fecha_limite_parcial',
        'fecha_limite_final',
        'horas_requeridas',
        'horas_completadas',
        'estatus',
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

    public function comentarios()
    {
        return $this->morphMany(Comentario::class, 'comentable');
    }

    public function gradoAcademicoJefe()
    {
        return $this->belongsTo(GradoAcademico::class, 'grado_academico_jefe_id');
    }

    public function horario()
    {
        return $this->belongsTo(Horario::class);
    }

    public function documentosCompletos()
    {
        $documentosRequeridos = [
            'Solicitud de Prácticas Profesionales',
            'Elección de Modalidad',
            'Carta de Presentación de Prácticas Profesionales',
            'Carta de Aceptación',
            'Evaluación de Competencias del Desempeño',
            'Carta de Liberación de Prácticas Profesionales',
            'Primer Informe de Actividades',
            'Segundo Informe de Actividades'
        ];

        $subidos = Documento::where('user_id', $this->user_id)
            ->where('activo', true)
            ->whereHas('tipoDocumento', function($q) use ($documentosRequeridos) {
                $q->whereIn('nombre', $documentosRequeridos)
                  ->where('tramite', 'PP');
            })
            ->count();

        return $subidos === count($documentosRequeridos);
    }

    protected static function booted()
    {
        static::created(function ($practica) {
            $practica->user->update([
                'estatus_practicas' => $practica->estatus
            ]);
        });

        static::updating(function ($practica) {
            if ($practica->isDirty('estatus')) {
                $practica->user()->update([
                    'estatus_practicas' => $practica->estatus
                ]);
            }
        });

        static::deleted(function ($practica) {
            $practica->user->update([
                'estatus_practicas' => 'no_solicitado'
            ]);
        });
    }
}