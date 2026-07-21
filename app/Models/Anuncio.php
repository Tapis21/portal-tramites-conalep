<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Anuncio extends Model
{
    use HasFactory;

    protected $fillable = ['contenido', 'admin_id'];

    public function admin()
    {
        return $this->belongsTo(User::class, 'admin_id');
    }

    // Relación con usuarios a través de anuncio_user (vistos)
    public function usuarios()
    {
        return $this->belongsToMany(User::class, 'anuncio_user')
                    ->withPivot('visto', 'visto_at')
                    ->withTimestamps();
    }

    // Verificar si un usuario específico ha visto este anuncio
    public function vistoPor(User $user): bool
    {
        return $this->usuarios()
                    ->where('user_id', $user->id)
                    ->wherePivot('visto', true)
                    ->exists();
    }

    // Marcar como visto por un usuario
    public function marcarComoVisto(User $user): void
    {
        $this->usuarios()->syncWithoutDetaching([
            $user->id => ['visto' => true, 'visto_at' => now()]
        ]);
    }
}