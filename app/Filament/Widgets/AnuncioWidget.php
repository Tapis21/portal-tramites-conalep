<?php

namespace App\Filament\Widgets;

use App\Models\Anuncio;
use Filament\Widgets\Widget;
use Illuminate\Support\Facades\Auth;

class AnunciosWidget extends Widget
{
    protected int | string | array $columnSpan = 'full';

    protected static ?int $sort = 1;

    public function getViewData(): array
    {
        $user = Auth::user();

        $anuncios = Anuncio::recientes()
            ->with('admin')
            ->take(5)
            ->get();

        // Marcar como vistos los anuncios mostrados
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

    protected function getView(): string
    {
        return 'filament.widgets.anuncios-widget';
    }
}