<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EstudiantePeriodo extends Model
{
    use HasFactory;

    protected $table = 'estudiante_periodo';

    protected $fillable = [
        'user_id',
        'periodo_id',
        'activo',
        'fecha_asignacion'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function periodo()
    {
        return $this->belongsTo(Periodo::class);
    }
}