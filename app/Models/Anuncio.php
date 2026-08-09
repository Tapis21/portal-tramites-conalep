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

    public function usuarios()
    {
        return $this->belongsToMany(User::class, 'anuncio_user')
                    ->withPivot('visto', 'visto_at')
                    ->withTimestamps();
    }

    public function vistoPor(User $user): bool
    {
        return $this->usuarios()
                    ->where('user_id', $user->id)
                    ->wherePivot('visto', true)
                    ->exists();
    }

    public function marcarComoVisto(User $user): void
    {
        $this->usuarios()->syncWithoutDetaching([
            $user->id => ['visto' => true, 'visto_at' => now()]
        ]);
    }

    // Scope para obtener anuncios recientes (usando created_at)
    public function scopeRecientes($query)
    {
        return $query->orderBy('created_at', 'desc');
    }
}