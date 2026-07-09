<?php

namespace App\Filament\Resources\Users\Tables;

use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Table;
use Filament\Actions\Action;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;

class UsersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('matricula')
                    ->label('Matrícula')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('name')
                    ->label('Nombre')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('apellidos')
                    ->label('Apellidos'),

                TextColumn::make('email')
                    ->label('Email'),

                TextColumn::make('role')
                    ->label('Rol')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'admin' => 'success',
                        'estudiante' => 'info',
                        default => 'gray',
                    }),

                // ✅ COLUMNA: Contraseña (muestra matrícula o "🔒 Cambiada")
                TextColumn::make('password_display')
                    ->label('Contraseña')
                    ->formatStateUsing(function ($record) {
                        if ($record->password_changed_at) {
                            return '🔒 Cambiada';
                        }
                        return $record->matricula;
                    })
                    ->color(fn ($record) => $record->password_changed_at ? 'gray' : 'warning')
                    ->tooltip(fn ($record) => $record->password_changed_at 
                        ? 'La contraseña fue cambiada por el usuario o administrador'
                        : 'Contraseña por defecto (matrícula)'
                    )
                    ->copyable()
                    ->copyMessage('Contraseña copiada al portapapeles')
                    ->copyMessageDuration(2000),

                IconColumn::make('password_changed_at')
                    ->label('¿Cambiada?')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger')
                    ->state(fn ($record) => $record->password_changed_at !== null)
                    ->tooltip(fn ($record) => $record->password_changed_at
                        ? 'Cambiada el: ' . $record->password_changed_at->format('d/m/Y H:i')
                        : 'Contraseña por defecto (matrícula)'
                    ),

                BadgeColumn::make('estatus_servicio_social')
                    ->label('Estatus SS')
                    ->colors([
                        'success' => 'liberado',
                        'warning' => 'en_progreso',
                        'danger' => 'pendiente',
                        'gray' => 'no_solicitado',
                    ])
                    ->formatStateUsing(fn ($state) => match ($state) {
                        'liberado' => '✅ Liberado',
                        'en_progreso' => '🔄 En progreso',
                        'pendiente' => '⏳ Pendiente',
                        'no_solicitado' => '📋 No solicitado',
                        default => $state,
                    }),

                BadgeColumn::make('estatus_practicas')
                    ->label('Estatus PP')
                    ->colors([
                        'success' => 'liberado',
                        'warning' => 'en_progreso',
                        'danger' => 'pendiente',
                        'gray' => 'no_solicitado',
                    ])
                    ->formatStateUsing(fn ($state) => match ($state) {
                        'liberado' => '✅ Liberado',
                        'en_progreso' => '🔄 En progreso',
                        'pendiente' => '⏳ Pendiente',
                        'no_solicitado' => '📋 No solicitado',
                        default => $state,
                    }),

                TextColumn::make('periodo')
                    ->label('Periodo')
                    ->placeholder('—')
                    ->badge()
                    ->color('gray')
                    ->getStateUsing(function ($record) {
                        $periodo = $record->periodoActual();
                        return $periodo ? $periodo->nombre : null;
                    }),
            ])
            ->actions([
                // ✅ ACCIÓN: Restablecer contraseña (envía email)
                Action::make('reset_password')
                    ->label('Restablecer contraseña')
                    ->icon('heroicon-o-envelope')
                    ->color('warning')
                    ->requiresConfirmation()
                    ->modalHeading('Restablecer contraseña')
                    ->modalDescription(fn ($record) => '¿Deseas enviar un enlace de restablecimiento de contraseña a: ' . $record->email . '?')
                    ->modalSubmitActionLabel('Enviar enlace')
                    ->action(function ($record) {
                        $status = Password::sendResetLink(
                            ['email' => $record->email]
                        );

                        if ($status === Password::RESET_LINK_SENT) {
                            Notification::make()
                                ->title('✅ Enlace enviado')
                                ->body("Se ha enviado un enlace de restablecimiento a {$record->email}")
                                ->success()
                                ->send();
                        } else {
                            Notification::make()
                                ->title('❌ Error')
                                ->body('No se pudo enviar el enlace. Verifica que el email sea válido.')
                                ->danger()
                                ->send();
                        }
                    }),

                // ✅ ACCIÓN: Cambiar contraseña manual
                Action::make('change_password')
                    ->label('Cambiar contraseña')
                    ->icon('heroicon-o-key')
                    ->color('primary')
                    ->form([
                        TextInput::make('new_password')
                            ->label('Nueva contraseña')
                            ->password()
                            ->required()
                            ->minLength(8)
                            ->helperText('Mínimo 8 caracteres'),
                        TextInput::make('confirm_password')
                            ->label('Confirmar contraseña')
                            ->password()
                            ->required()
                            ->same('new_password'),
                    ])
                    ->action(function (array $data, $record) {
                        $record->update([
                            'password' => Hash::make($data['new_password']),
                            'password_changed_at' => now(),
                        ]);

                        Notification::make()
                            ->title('✅ Contraseña cambiada')
                            ->body("La contraseña de {$record->name} ha sido cambiada correctamente.")
                            ->success()
                            ->send();
                    })
                    ->modalHeading('Cambiar contraseña')
                    ->modalSubmitActionLabel('Cambiar contraseña')
                    ->modalWidth('md'),

                // ✅ ACCIÓN: Editar
                Action::make('editar')
                    ->label('Editar')
                    ->icon('heroicon-o-pencil-square')
                    ->color('warning')
                    ->url(fn ($record) => route('filament.admin.resources.users.edit', $record)),

                // ✅ ACCIÓN: Eliminar
                Action::make('eliminar')
                    ->label('Eliminar')
                    ->icon('heroicon-o-trash')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->modalHeading('Eliminar usuario')
                    ->modalDescription(fn ($record) => '¿Estás seguro de eliminar a ' . $record->name . '?')
                    ->modalSubmitActionLabel('Sí, eliminar')
                    ->action(function ($record) {
                        $record->delete();
                        Notification::make()
                            ->title('✅ Usuario eliminado')
                            ->body("El usuario ha sido eliminado correctamente.")
                            ->success()
                            ->send();
                    }),
            ])
            ->paginated([25, 50, 100])
            ->defaultSort('name');
    }
}