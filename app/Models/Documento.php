<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Documento extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'tipo_documento_id',
        'archivo_pdf',
        'estatus',
        'comentario'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function tipoDocumento()
    {
        return $this->belongsTo(TipoDocumento::class);
    }

    public function comentarios()
    {
        return $this->morphMany(Comentario::class, 'comentable');
    }
}