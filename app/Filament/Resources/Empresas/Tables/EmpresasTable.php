<?php

namespace App\Filament\Resources\Empresas\Tables;

use App\Models\Empresa;
use Carbon\Carbon;
use Filament\Actions\Action;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\Empresas\Schemas\EmpresaForm;

class EmpresasTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->query(Empresa::query()->orderBy('nombre'))
            ->columns([
                TextColumn::make('nombre')
                    ->label('Empresa')
                    ->searchable()
                    ->sortable()
                    ->formatStateUsing(fn ($state) => '🏢 ' . $state)
                    ->extraAttributes(['class' => 'font-semibold text-gray-800 text-sm']),

                TextColumn::make('direccion')
                    ->label('Dirección')
                    ->icon('heroicon-o-map-pin')
                    ->iconColor('gray')
                    ->limit(40)
                    ->tooltip(fn ($record) => $record->direccion)
                    ->placeholder('—'),

                TextColumn::make('telefono')
                    ->label('Teléfono')
                    ->icon('heroicon-o-phone')
                    ->iconColor('gray')
                    ->placeholder('—'),

                TextColumn::make('contacto')
                    ->label('Contacto')
                    ->icon('heroicon-o-user')
                    ->iconColor('gray')
                    ->placeholder('—'),

                TextColumn::make('servicios')
                    ->label('Servicios')
                    ->formatStateUsing(function ($record) {
                        $badges = [];
                        if ($record->servicio_social) $badges[] = '🟢 SS';
                        if ($record->practicas) $badges[] = '🟠 PP';
                        if ($record->programa_dual) $badges[] = '🟣 Dual';
                        return implode(' ', $badges) ?: '⚪ Ninguno';
                    })
                    ->badge()
                    ->color(fn ($record) => 
                        $record->servicio_social || $record->practicas || $record->programa_dual ? 'gray' : 'gray'
                    ),

                TextColumn::make('fecha_termino_convenio')
                    ->label('Convenio')
                    ->date('d/m/Y')
                    ->placeholder('♾️ Indefinido')
                    ->icon('heroicon-o-calendar')
                    ->iconColor('gray')
                    ->color(function ($record) {
                        if (!$record->fecha_termino_convenio) {
                            return 'success';
                        }
                        return $record->fecha_termino_convenio->isPast() ? 'danger' : 'success';
                    })
                    ->tooltip(function ($record) {
                        if (!$record->fecha_termino_convenio) {
                            return 'Convenio vigente indefinido';
                        }
                        $dias = Carbon::now()->diffInDays($record->fecha_termino_convenio, false);
                        if ($dias < 0) {
                            return '⚠️ Convenio vencido hace ' . abs(intval($dias)) . ' días';
                        }
                        return 'Vence en ' . intval($dias) . ' días';
                    }),

                IconColumn::make('activo')
                    ->label('Activo')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger')
                    ->tooltip(fn ($record) => $record->activo ? 'Activo' : 'Inactivo'),
            ])
            ->filters([
                SelectFilter::make('tipo')
                    ->label('Tipo de convenio')
                    ->options([
                        'ss' => '🟢 Servicio Social',
                        'pp' => '🟠 Prácticas Profesionales',
                        'ambos' => '✅ Ambos',
                        'ninguno' => '⚪ Ninguno',
                    ])
                    ->query(function (Builder $query, array $data) {
                        if ($data['value'] === 'ss') {
                            $query->where('servicio_social', true);
                        } elseif ($data['value'] === 'pp') {
                            $query->where('practicas', true);
                        } elseif ($data['value'] === 'ambos') {
                            $query->where('servicio_social', true)->where('practicas', true);
                        } elseif ($data['value'] === 'ninguno') {
                            $query->where('servicio_social', false)->where('practicas', false);
                        }
                    }),

                SelectFilter::make('activo')
                    ->label('Estado')
                    ->options([
                        '1' => '✅ Activas',
                        '0' => '❌ Inactivas',
                    ])
                    ->query(function (Builder $query, array $data) {
                        if ($data['value'] === '1') {
                            $query->where('activo', true);
                        } elseif ($data['value'] === '0') {
                            $query->where('activo', false);
                        }
                    }),
            ])
            ->headerActions([
                Action::make('crear_empresa')
                    ->label('Nueva Empresa')
                    ->icon('heroicon-o-plus-circle')
                    ->color('success')
                    ->modalHeading('Registrar Nueva Empresa')
                    ->modalDescription('Completa los datos para registrar una nueva empresa aliada')
                    ->modalSubmitActionLabel('Guardar Empresa')
                    ->modalCancelActionLabel('Cancelar')
                    ->modalWidth('2xl')
                    ->form(function () {
                        return EmpresaForm::configure(Schema::make())->getComponents();
                    })
                    ->action(function (array $data) {
                        Empresa::create($data);
                        Notification::make()
                            ->title('✅ Empresa creada')
                            ->body("Se ha registrado la empresa: {$data['nombre']}")
                            ->success()
                            ->send();
                    }),
            ])
            ->actions([
                Action::make('editar')
                    ->label('Editar')
                    ->icon('heroicon-o-pencil')
                    ->color('warning')
                    ->modalHeading('Editar Empresa')
                    ->modalSubmitActionLabel('Actualizar')
                    ->modalCancelActionLabel('Cancelar')
                    ->modalWidth('2xl')
                    ->form(function ($record) {
                        return EmpresaForm::configure(Schema::make(), $record)->getComponents();
                    })
                    ->action(function (array $data, $record) {
                        $record->update($data);
                        Notification::make()
                            ->title('✅ Empresa actualizada')
                            ->body("Se ha actualizado la empresa: {$data['nombre']}")
                            ->success()
                            ->send();
                    }),

                Action::make('ver')
                    ->label('Ver')
                    ->icon('heroicon-o-eye')
                    ->color('info')
                    ->url(fn ($record) => route('filament.admin.resources.empresas.view', $record))
                    ->openUrlInNewTab(false),

                Action::make('eliminar')
                    ->label('Eliminar')
                    ->icon('heroicon-o-trash')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->modalHeading('Eliminar Empresa')
                    ->modalDescription('¿Estás seguro de eliminar esta empresa? Esta acción no se puede deshacer.')
                    ->modalSubmitActionLabel('Sí, eliminar')
                    ->modalCancelActionLabel('Cancelar')
                    ->action(function ($record) {
                        $nombre = $record->nombre;
                        $record->delete();
                        Notification::make()
                            ->title('🗑️ Empresa eliminada')
                            ->body("Se ha eliminado la empresa: {$nombre}")
                            ->success()
                            ->send();
                    }),
            ])
            ->bulkActions([
                Action::make('eliminar_seleccionadas')
                    ->label('Eliminar seleccionadas')
                    ->icon('heroicon-o-trash')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->modalHeading('Eliminar empresas seleccionadas')
                    ->modalDescription('¿Estás seguro de eliminar estas empresas? Esta acción no se puede deshacer.')
                    ->action(function ($records) {
                        $count = $records->count();
                        $records->each->delete();
                        Notification::make()
                            ->title('🗑️ Empresas eliminadas')
                            ->body("Se han eliminado {$count} empresas")
                            ->success()
                            ->send();
                    }),
            ])
            ->defaultSort('nombre')
            ->striped()
            ->paginated([15, 25, 50, 100])
            ->emptyStateHeading('No hay empresas registradas')
            ->emptyStateDescription('Registra tu primera empresa aliada para comenzar.')
            ->emptyStateIcon('heroicon-o-building-office');
    }
}