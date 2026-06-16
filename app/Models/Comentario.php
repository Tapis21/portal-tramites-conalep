<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Comentario extends Model
{
    use HasFactory;

    protected $fillable = [
        'contenido',
        'tipo',
        'comentable_id',
        'comentable_type',
        'user_id',
        'leido', // Nuevo campo para marcar como leído
        'leido_at', // Nuevo campo para almacenar la fecha de lectura
    ];

    protected $casts = [
        'leido' => 'boolean',
        'leido_at' => 'datetime',
    ];

    public function comentable()
    {
        return $this->morphTo();
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Scope para comentarios no leídos
    public function scopeNoLeidos($query)
    {
        return $query->where('leido', false);
    }

    // Scope para comentarios de admin
    public function scopeAdmin($query)
    {
        return $query->where('tipo', 'like', 'admin%');
    }

    // Marcar como leído
    public function marcarComoLeido()
    {
        $this->update([
            'leido' => true,
            'leido_at' => now(),
        ]);
    }
}