<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CartaPresentacion extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'empresa',
        'direccion',
        'contacto',
        'puesto',
        'fecha_inicio',
        'fecha_termino',
        'pdf_generado'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}