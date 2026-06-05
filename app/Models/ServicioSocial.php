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
        'horas_requeridas',
        'horas_completadas',
        'reporte_parcial_subido',
        'reporte_parcial_validado',
        'reporte_final_subido',
        'reporte_final_validado',
        'archivo_parcial',
        'archivo_final',
        'estatus'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function comentarios()
    {
        return $this->morphMany(Comentario::class, 'comentable');
    }
}