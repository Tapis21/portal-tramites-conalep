<?php

namespace App\Filament\Resources\ServicioSocials\Pages;

use App\Filament\Resources\ServicioSocials\ServicioSocialResource;
use App\Models\ServicioSocial;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditServicioSocial extends EditRecord
{
    protected static string $resource = ServicioSocialResource::class;

    // ✅ CORREGIDO: getModel con tipo de retorno correcto
    public function getModel(): string
    {
        return ServicioSocial::class;
    }

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}