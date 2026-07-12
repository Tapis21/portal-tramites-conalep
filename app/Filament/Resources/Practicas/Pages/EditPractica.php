<?php

namespace App\Filament\Resources\Practicas\Pages;

use App\Filament\Resources\Practicas\PracticaResource;
use App\Models\Practica;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditPractica extends EditRecord
{
    protected static string $resource = PracticaResource::class;

    // ✅ CORREGIDO: getModel con tipo de retorno correcto
    public function getModel(): string
    {
        return Practica::class;
    }

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}