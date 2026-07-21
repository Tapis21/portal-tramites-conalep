<?php

namespace App\Filament\Resources\Periodos\Pages;

use App\Filament\Resources\Periodos\PeriodoResource;
use App\Models\Periodo;
use App\Models\User;
use Filament\Resources\Pages\Page;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Actions\Action;
use Illuminate\Support\Facades\Hash;
use Filament\Notifications\Notification;

class ViewUsuariosPeriodo extends Page implements HasTable
{
    use InteractsWithTable;

    protected static string $resource = PeriodoResource::class;

    // ✅ CORREGIDO: $view como string (no estática)
    protected string $view = 'filament.resources.periodos.pages.view-usuarios-periodo';

    public Periodo $record;

    // ✅ CORREGIDO: getTitle como método no estático
    public function getTitle(): string
    {
        return "Usuarios del periodo: {$this->record->nombre}";
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(
                User::query()
                    ->whereHas('periodos', function ($q) {
                        $q->where('periodo_id', $this->record->id);
                    })
                    ->with('servicioSocial')
                    ->with('practicas')
            )
            ->columns([
                TextColumn::make('name')
                    ->label('Estudiante')
                    ->formatStateUsing(fn ($record) => $record->name . ' ' . $record->apellidos)
                    ->searchable()
                    ->sortable(),

                TextColumn::make('matricula')
                    ->label('Matrícula')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('grupo')
                    ->label('Grupo')
                    ->searchable()
                    ->sortable(),

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
            ])
            ->actions([
                Action::make('editar_usuario')
                    ->label('Editar')
                    ->icon('heroicon-o-pencil-square')
                    ->color('warning')
                    ->url(fn ($record) => route('filament.admin.resources.users.edit', $record)),

                Action::make('cambiar_password')
                    ->label('Cambiar contraseña')
                    ->icon('heroicon-o-key')
                    ->color('primary')
                    ->form([
                        \Filament\Forms\Components\TextInput::make('new_password')
                            ->label('Nueva contraseña')
                            ->password()
                            ->required()
                            ->minLength(8)
                            ->helperText('Mínimo 8 caracteres'),
                        \Filament\Forms\Components\TextInput::make('confirm_password')
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
                    }),

                Action::make('eliminar_usuario')
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