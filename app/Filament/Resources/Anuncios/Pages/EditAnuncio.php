<?php

namespace App\Filament\Resources\Anuncios\Pages;

use App\Filament\Resources\Anuncios\AnuncioResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditAnuncio extends EditRecord
{
    protected static string $resource = AnuncioResource::class;

    public function getView(): string
    {
        return 'filament.resources.anuncios.form';
    }

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make()
                ->label('Eliminar anuncio')
                ->icon('heroicon-o-trash'),
        ];
    }
}