<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GradoAcademico extends Model
{
    use HasFactory;

    protected $table = 'grados_academicos';

    protected $fillable = ['nombre', 'abreviatura', 'activo'];
}
