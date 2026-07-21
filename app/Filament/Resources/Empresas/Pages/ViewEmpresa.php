<?php

namespace App\Filament\Resources\Empresas\Pages;

use App\Filament\Resources\Empresas\EmpresaResource;
use Filament\Resources\Pages\ViewRecord;

class ViewEmpresa extends ViewRecord
{
    protected static string $resource = EmpresaResource::class;

    protected string $view = 'filament.empresas.view-modal-ver-empresa';

    public function getTitle(): string
    {
        return "🏢 " . $this->record->nombre;
    }
}