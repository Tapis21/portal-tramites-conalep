<?php

namespace App\Filament\Resources\Users\Pages;

use App\Filament\Resources\Users\UserResource;
use App\Models\EstudiantePeriodo;
use Filament\Resources\Pages\CreateRecord;

class CreateUser extends CreateRecord
{
    protected static string $resource = UserResource::class;

    // ✅ DESPUÉS DE CREAR, ASIGNAR EL PERIODO
    protected function afterCreate(): void
    {
        $data = $this->form->getState();
        $record = $this->record;

        if (isset($data['periodo_id']) && $data['periodo_id']) {
            EstudiantePeriodo::create([
                'user_id' => $record->id,
                'periodo_id' => $data['periodo_id'],
                'estatus' => 'cursando',
            ]);
        }
    }
}