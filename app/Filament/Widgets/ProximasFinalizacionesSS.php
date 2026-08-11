<?php

namespace App\Filament\Widgets;

use App\Models\ServicioSocial;
use Carbon\Carbon;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Actions\Action;
use Filament\Widgets\TableWidget as BaseWidget;

class ProximasFinalizacionesSS extends BaseWidget
{
    protected static ?int $sort = 4;

    public function table(Table $table): Table
    {
        return $table
            ->query(
                ServicioSocial::query()
                    ->where('estatus', 'en_progreso')
                    ->whereNotNull('fecha_limite_segundo_informe')
                    ->with('user')
                    ->with('empresa')
                    ->orderBy('fecha_limite_segundo_informe', 'asc')
            )
            ->columns([
                TextColumn::make('user.name')
                    ->label('Alumno')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('user.matricula')
                    ->label('Matrícula')
                    ->searchable(),
                TextColumn::make('empresa.nombre')
                    ->label('Empresa')
                    ->searchable()
                    ->placeholder('Sin asignar'),
                TextColumn::make('fecha_limite_segundo_informe')
                    ->label('Finaliza')
                    ->date('d/m/Y')
                    ->sortable()
                    ->color(function ($record) {
                        $dias = Carbon::now()->diffInDays($record->fecha_limite_segundo_informe);
                        if ($dias <= 7) return 'danger';
                        if ($dias <= 15) return 'warning';
                        return 'success';
                    }),
                TextColumn::make('dias_restantes')
                    ->label('Días')
                    ->state(fn ($record) => Carbon::now()->diffInDays($record->fecha_limite_segundo_informe))
                    ->badge()
                    ->color(function ($state) {
                        if ($state <= 7) return 'danger';
                        if ($state <= 15) return 'warning';
                        return 'success';
                    })
                    ->formatStateUsing(fn ($state) => $state . ' días'),
            ])
            ->actions([
                Action::make('ver')
                    ->label('Ver')
                    ->icon('heroicon-o-eye')
                    ->color('primary')
                    ->url(fn ($record) => route('filament.admin.resources.servicio-socials.view', $record->user))
                    ->openUrlInNewTab(false),
            ])
            ->emptyStateHeading('No hay próximas finalizaciones')
            ->emptyStateIcon('heroicon-o-calendar')
            ->heading('Próximas Finalizaciones SS');
    }
}