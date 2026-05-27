<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TipoDocumento extends Model
{
    use HasFactory;

    protected $fillable = ['nombre', 'tramite'];

    public function documentos()
    {
        return $this->hasMany(Documento::class);
    }
}