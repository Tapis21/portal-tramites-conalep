<?php

namespace App\Filament\Widgets;

use App\Models\Practica;
use Carbon\Carbon;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Actions\Action;
use Filament\Widgets\TableWidget as BaseWidget;

class ProximasFinalizacionesPP extends BaseWidget
{
    protected static ?int $sort = 5;

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Practica::query()
                    ->where('estatus', 'en_progreso')
                    ->whereNotNull('fecha_limite_final')
                    ->with('user')
                    ->with('empresa')
                    ->orderBy('fecha_limite_final', 'asc')
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
                TextColumn::make('fecha_limite_final')
                    ->label('Finaliza')
                    ->date('d/m/Y')
                    ->sortable()
                    ->color(fn ($record) => match (true) {
                        Carbon::now()->diffInDays($record->fecha_limite_final) <= 7 => 'danger',
                        Carbon::now()->diffInDays($record->fecha_limite_final) <= 15 => 'warning',
                        default => 'success',
                    }),
                TextColumn::make('dias_restantes')
                    ->label('Días')
                    ->state(fn ($record) => Carbon::now()->diffInDays($record->fecha_limite_final))
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
                    ->url(fn ($record) => route('filament.admin.resources.practicas.view', $record->user))
                    ->openUrlInNewTab(false),
            ])
            ->emptyStateHeading('No hay próximas finalizaciones')
            ->emptyStateIcon('heroicon-o-calendar')
            ->heading('Próximas Finalizaciones PP');
    }
}