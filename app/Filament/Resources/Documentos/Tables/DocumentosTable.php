<?php

namespace App\Filament\Resources\Documentos\Tables;

use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class DocumentoTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('user.name')
                    ->label('Estudiante')
                    ->searchable(),
                TextColumn::make('tipoDocumento.nombre')
                    ->label('Documento')
                    ->searchable(),
                TextColumn::make('archivo_pdf')
                    ->label('Archivo')
                    ->formatStateUsing(fn ($state) => $state ? '📄 Ver PDF' : 'Sin archivo')
                    ->url(fn ($record) => $record->archivo_pdf ? asset('storage/' . $record->archivo_pdf) : null)
                    ->openUrlInNewTab(),
                TextColumn::make('estatus')
                    ->label('Estado')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'validado' => 'success',
                        'rechazado' => 'danger',
                        default => 'warning',
                    }),
                TextColumn::make('created_at')
                    ->label('Subido')
                    ->date('d/m/Y H:i'),
            ])
            ->paginated(false)  // EVITA intl
            ->defaultSort('created_at', 'desc');
    }
}