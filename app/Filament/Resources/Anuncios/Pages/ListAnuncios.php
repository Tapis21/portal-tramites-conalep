<?php

namespace App\Filament\Resources\Anuncios\Pages;

use App\Filament\Resources\Anuncios\AnuncioResource;
use App\Models\Anuncio;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\Facades\Auth;

class ListAnuncios extends ListRecords
{
    protected static string $resource = AnuncioResource::class;

    public function getView(): string
    {
        return 'filament.resources.anuncios.cards';
    }

    public function getViewData(): array
    {
        $user = Auth::user();

        $anuncios = Anuncio::with('admin')
            ->orderBy('created_at', 'desc')
            ->get();

        foreach ($anuncios as $anuncio) {
            if (!$anuncio->vistoPor($user)) {
                $anuncio->marcarComoVisto($user);
            }
        }

        return [
            'anuncios' => $anuncios,
            'user' => $user,
        ];
    }
}