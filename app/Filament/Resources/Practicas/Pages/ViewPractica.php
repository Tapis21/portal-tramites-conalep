<?php

namespace App\Filament\Resources\Practicas\Pages;

use App\Filament\Resources\Practicas\PracticaResource;
use Filament\Actions;
use Filament\Actions\Action;
use Filament\Forms\Components\Textarea;
use Filament\Infolists\Components\TextEntry;    // ✅ CORRECTO
use Filament\Schemas\Components\Section;         // ✅ CORRECTO
use Filament\Schemas\Schema;                     // ✅ CORRECTO
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use App\Models\Documento;
use Illuminate\Support\Facades\Auth;

class ViewPractica extends ViewRecord implements HasTable
{
    use InteractsWithTable;

    protected static string $resource = PracticaResource::class;

    public function infolist(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Section::make('Datos del Estudiante')
                    ->schema([
                        TextEntry::make('user.name')
                            ->label('Nombre completo'),
                        TextEntry::make('user.matricula')
                            ->label('Matrícula'),
                        TextEntry::make('user.carrera')
                            ->label('Carrera'),
                        TextEntry::make('user.semestre')
                            ->label('Semestre')
                            ->formatStateUsing(fn ($state) => $state . '° Semestre'),
                        TextEntry::make('user.grupo')
                            ->label('Grupo')
                            ->placeholder('No asignado'),
                        TextEntry::make('user.nombre_turno')
                            ->label('Turno'),
                    ])
                    ->columns(3),

                Section::make('Datos de la Empresa')
                    ->schema([
                        TextEntry::make('empresa.nombre')
                            ->label('Empresa'),
                        TextEntry::make('area_asignada')
                            ->label('Área asignada'),
                    ])
                    ->columns(2),

                Section::make('Fechas y Horas')
                    ->schema([
                        TextEntry::make('fecha_inicio')
                            ->label('Fecha de inicio')
                            ->date('d/m/Y'),
                        TextEntry::make('fecha_limite_final')
                            ->label('Fecha de finalización')
                            ->date('d/m/Y'),
                        TextEntry::make('horario.hora_inicio')
                            ->label('Horario')
                            ->formatStateUsing(fn ($record) => 
                                $record->horario ? $record->horario->hora_inicio . ' - ' . $record->horario->hora_fin : 'No definido'
                            ),
                        TextEntry::make('horas_requeridas')
                            ->label('Horas requeridas'),
                        TextEntry::make('horas_completadas')
                            ->label('Horas completadas'),
                    ])
                    ->columns(3),

                Section::make('Datos de Contacto')
                    ->schema([
                        TextEntry::make('gradoAcademico.abreviatura')
                            ->label('Grado (Carta)'),
                        TextEntry::make('nombre_persona_carta')
                            ->label('Nombre de la persona'),
                        TextEntry::make('cargo_persona_carta')
                            ->label('Cargo de la persona'),
                        TextEntry::make('gradoAcademicoJefe.abreviatura')
                            ->label('Grado (Jefe)'),
                        TextEntry::make('nombre_jefe_inmediato')
                            ->label('Nombre del jefe inmediato'),
                        TextEntry::make('cargo_jefe_inmediato')
                            ->label('Cargo del jefe inmediato'),
                    ])
                    ->columns(3),

                Section::make('Información Adicional')
                    ->schema([
                        TextEntry::make('apoyo_estudiante')
                            ->label('Apoyo al estudiante')
                            ->placeholder('No especificado'),
                        TextEntry::make('estatus')
                            ->label('Estatus')
                            ->badge()
                            ->color(fn (string $state): string => match ($state) {
                                'liberado' => 'success',
                                'en_progreso' => 'info',
                                'pendiente' => 'warning',
                                'no_solicitado' => 'gray',
                                default => 'gray',
                            })
                            ->formatStateUsing(fn (string $state): string => match ($state) {
                                'liberado' => '✅ Liberado',
                                'en_progreso' => '🔄 En progreso',
                                'pendiente' => '⏳ Pendiente',
                                'no_solicitado' => '⬜ No solicitado',
                                default => $state,
                            }),
                    ])
                    ->columns(2),
            ]);
    }

    // 👇 TABLA DE DOCUMENTOS
    public function table(Table $table): Table
    {
        return $table
            ->query(
                Documento::query()
                    ->where('user_id', $this->record->user_id)
                    ->where('activo', true)
                    ->whereHas('tipoDocumento', function($q) {
                        $q->where('tramite', 'PP');
                    })
                    ->with('tipoDocumento')
            )
            ->columns([
                TextColumn::make('tipoDocumento.nombre')
                    ->label('Documento')
                    ->searchable(),
                
                IconColumn::make('archivo_pdf')
                    ->label('Subido')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger')
                    ->state(fn ($record) => !is_null($record->archivo_pdf)),
                
                TextColumn::make('estatus')
                    ->label('Estatus')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'validado' => 'success',
                        'rechazado' => 'danger',
                        'pendiente' => 'warning',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'validado' => '✅ Validado',
                        'rechazado' => '❌ Rechazado',
                        'pendiente' => '⏳ Pendiente',
                        default => $state,
                    }),
            ])
            ->actions([
                Action::make('ver_documento')
                    ->label('Ver PDF')
                    ->icon('heroicon-o-eye')
                    ->color('primary')
                    ->modalHeading('Visualizar Documento')
                    ->modalContent(function ($record) {
                        $url = $record->archivo_pdf ? asset('storage/' . $record->archivo_pdf) : null;
                        
                        if (!$url) {
                            return view('filament.modals.documento-no-disponible');
                        }
                        
                        return view('filament.modals.documento-pdf', ['url' => $url, 'documento' => $record]);
                    })
                    ->modalSubmitAction(false)
                    ->modalCancelActionLabel('Cerrar'),

                Action::make('validar')
                    ->label('Validar')
                    ->color('success')
                    ->icon('heroicon-o-check')
                    ->hidden(fn ($record) => $record->estatus !== 'pendiente' && $record->estatus !== 'rechazado')
                    ->requiresConfirmation()
                    ->form([
                        Textarea::make('comentario')
                            ->label('Comentario (opcional)')
                            ->placeholder('Agrega un comentario sobre la validación...')
                            ->maxLength(500),
                    ])
                    ->action(function ($record, array $data) {
                        $record->update([
                            'estatus' => 'validado',
                            'comentario_admin' => $data['comentario'] ?? null,
                        ]);

                        if (!empty($data['comentario'])) {
                            \App\Models\Comentario::create([
                                'contenido' => $data['comentario'],
                                'tipo' => 'admin',
                                'user_id' => Auth::id(),
                                'comentable_id' => $record->id,
                                'comentable_type' => 'App\Models\Documento',
                            ]);
                        }

                        Notification::make()
                            ->title('Documento validado')
                            ->body('El documento ha sido validado correctamente.')
                            ->success()
                            ->send();
                    }),

                Action::make('rechazar')
                    ->label('Rechazar')
                    ->color('danger')
                    ->icon('heroicon-o-x-mark')
                    ->hidden(fn ($record) => $record->estatus !== 'pendiente' && $record->estatus !== 'validado')
                    ->requiresConfirmation()
                    ->form([
                        Textarea::make('comentario')
                            ->label('Motivo del rechazo')
                            ->placeholder('Explica por qué se rechaza este documento...')
                            ->required()
                            ->maxLength(500),
                    ])
                    ->action(function ($record, array $data) {
                        $record->update([
                            'estatus' => 'rechazado',
                            'comentario_admin' => $data['comentario'],
                        ]);

                        \App\Models\Comentario::create([
                            'contenido' => $data['comentario'],
                            'tipo' => 'admin',
                            'user_id' => Auth::id(),
                            'comentable_id' => $record->id,
                            'comentable_type' => 'App\Models\Documento',
                        ]);

                        Notification::make()
                            ->title('Documento rechazado')
                            ->body('El documento ha sido rechazado.')
                            ->danger()
                            ->send();
                    }),
            ])
            ->emptyStateHeading('No hay documentos subidos')
            ->emptyStateDescription('Este estudiante aún no ha subido ningún documento.')
            ->emptyStateIcon('heroicon-o-document-text');
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}