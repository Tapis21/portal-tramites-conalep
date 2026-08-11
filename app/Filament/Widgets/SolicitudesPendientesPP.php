<?php

namespace App\Filament\Widgets;

use App\Models\Practica;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Actions\Action;
use Filament\Widgets\TableWidget as BaseWidget;

class SolicitudesPendientesPP extends BaseWidget
{
    protected static ?int $sort = 3;

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Practica::query()
                    ->where('estatus', 'pendiente')
                    ->with('user')
                    ->with('empresa')
                    ->orderBy('created_at', 'desc')
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
                TextColumn::make('created_at')
                    ->label('Fecha de solicitud')
                    ->date('d/m/Y')
                    ->sortable(),
            ])
            ->actions([
                Action::make('ver')
                    ->label('Ver')
                    ->icon('heroicon-o-eye')
                    ->color('primary')
                    ->url(fn ($record) => route('filament.admin.resources.practicas.view', $record->user))
                    ->openUrlInNewTab(false),
            ])
            ->emptyStateHeading('No hay solicitudes pendientes')
            ->emptyStateIcon('heroicon-o-check-circle')
            ->heading('Solicitudes Pendientes PP');
    }
}