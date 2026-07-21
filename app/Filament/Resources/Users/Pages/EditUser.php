<?php

namespace App\Filament\Resources\Users\Pages;

use App\Filament\Resources\Users\UserResource;
use App\Models\EstudiantePeriodo;
use Filament\Resources\Pages\EditRecord;

class EditUser extends EditRecord
{
    protected static string $resource = UserResource::class;

    // ✅ DESPUÉS DE GUARDAR, ACTUALIZAR EL PERIODO
    protected function afterSave(): void
    {
        $data = $this->form->getState();
        $record = $this->record;

        if (isset($data['periodo_id']) && $data['periodo_id']) {
            // Buscar si ya tiene un periodo asignado
            $estudiantePeriodo = EstudiantePeriodo::where('user_id', $record->id)->first();

            if ($estudiantePeriodo) {
                // Actualizar periodo existente
                $estudiantePeriodo->update([
                    'periodo_id' => $data['periodo_id'],
                ]);
            } else {
                // Crear nuevo registro
                EstudiantePeriodo::create([
                    'user_id' => $record->id,
                    'periodo_id' => $data['periodo_id'],
                    'estatus' => 'cursando',
                ]);
            }
        }
    }
}