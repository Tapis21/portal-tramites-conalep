<?php

namespace App\Filament\Resources\ServicioSocials\Pages;

use App\Filament\Resources\ServicioSocials\ServicioSocialResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListServicioSocials extends ListRecords
{
    protected static string $resource = ServicioSocialResource::class;

    protected string $view = 'filament.resources.servicio-socials.pages.list-servicio-socials';

    protected function getHeaderActions(): array
    {
        return [
            // CreateAction::make(),
        ];
    }
}
