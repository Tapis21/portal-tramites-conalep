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

    /**
     * Verifica si todos los documentos obligatorios están subidos
     */
    public function documentosCompletos()
    {
        // Documentos administrativos requeridos (los que están en tabla documentos)
        $documentosRequeridos = [
            'Solicitud de Prácticas Profesionales',
            'Elección de Modalidad',
            'Carta de Presentación de Prácticas Profesionales',
            'Carta de Aceptación',
            'Evaluación de Competencias del Desempeño',
            'Carta de Liberación de Prácticas Profesionales'
        ];

        $subidos = Documento::where('user_id', $this->user_id)
            ->where('activo', true)
            ->whereHas('tipoDocumento', function($q) use ($documentosRequeridos) {
                $q->whereIn('nombre', $documentosRequeridos);
            })
            ->count();

        // Informes requeridos (Primer y Segundo Informe)
        $informesSubidos = $this->reporte_parcial_subido && $this->reporte_final_subido;

        return $subidos === count($documentosRequeridos) && $informesSubidos;
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