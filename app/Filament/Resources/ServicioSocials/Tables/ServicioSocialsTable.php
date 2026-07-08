<?php

namespace App\Filament\Resources\ServicioSocials\Tables;

use Carbon\Carbon;
use Filament\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use App\Models\User;

class ServicioSocialsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->query(
                User::query()
                    ->with('servicioSocial')
                    ->with('periodos')
            )
            ->columns([
                TextColumn::make('name')
                    ->label('Estudiante')
                    ->searchable()
                    ->sortable()
                    ->formatStateUsing(fn ($record) => $record->name . ' ' . $record->apellidos),

                TextColumn::make('matricula')
                    ->label('Matrícula')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('grupo')
                    ->label('Grupo')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('servicioSocial.empresa.nombre')
                    ->label('Empresa')
                    ->searchable()
                    ->sortable()
                    ->placeholder('—'),

                TextColumn::make('servicioSocial.fecha_inicio')
                    ->label('Inicio')
                    ->date('d/m/Y')
                    ->sortable()
                    ->placeholder('—'),

                TextColumn::make('servicioSocial.fecha_limite_segundo_informe')
                    ->label('Finaliza')
                    ->date('d/m/Y')
                    ->sortable()
                    ->placeholder('—')
                    ->color(fn ($record) => $record->servicioSocial ? self::getDaysColor($record->servicioSocial) : 'gray'),

                TextColumn::make('dias_restantes')
                    ->label('Días')
                    ->state(fn ($record) => $record->servicioSocial ? Carbon::now()->diffInDays($record->servicioSocial->fecha_limite_segundo_informe) : null)
                    ->badge()
                    ->color(fn ($state) => match (true) {
                        $state === null => 'gray',
                        $state <= 7 => 'danger',
                        $state <= 15 => 'warning',
                        default => 'success',
                    })
                    ->formatStateUsing(fn ($state) => $state !== null ? number_format($state, 2) . ' días' : '—'),

                BadgeColumn::make('estatus_servicio_social')
                    ->label('Estatus')
                    ->colors([
                        'success' => 'liberado',
                        'warning' => 'pendiente_revision',
                        'info' => 'en_progreso',
                        'gray' => 'no_solicitado',
                    ])
                    ->formatStateUsing(function ($state, $record) {
                        if ($record->servicioSocial) {
                            $estatus = $record->servicioSocial->estatus;
                            return match ($estatus) {
                                'liberado' => '✅ Liberado',
                                'pendiente_revision' => '⚠️ En Revisión',
                                'en_progreso' => '🔄 En Progreso',
                                'pendiente' => '⏳ Pendiente',
                                default => $estatus,
                            };
                        }
                        return '📋 No solicitado';
                    }),
            ])
            ->actions([
                Action::make('solicitar_ss')
                    ->label('Solicitar SS')
                    ->icon('heroicon-o-plus-circle')
                    ->color('success')
                    ->visible(fn ($record) => !$record->servicioSocial)
                    ->form([
                        Select::make('empresa_id')
                            ->label('Empresa')
                            ->options(
                                \App\Models\Empresa::where('activo', true)
                                    ->where('servicio_social', true)
                                    ->pluck('nombre', 'id')
                                    ->toArray()
                            )
                            ->required()
                            ->searchable()
                            ->helperText('Selecciona la empresa donde realizará el Servicio Social'),

                        DatePicker::make('fecha_inicio')
                            ->label('Fecha de inicio')
                            ->required()
                            ->default(now())
                            ->helperText('Fecha en que inicia el Servicio Social'),

                        DatePicker::make('fecha_limite_primer_informe')
                            ->label('Límite primer informe')
                            ->required()
                            ->default(now()->addMonths(3))
                            ->helperText('Fecha límite para entregar el primer informe'),

                        DatePicker::make('fecha_limite_segundo_informe')
                            ->label('Límite segundo informe')
                            ->required()
                            ->default(now()->addMonths(6))
                            ->helperText('Fecha límite para entregar el segundo informe'),

                        Select::make('grado_academico_id')
                            ->label('Grado académico (Carta)')
                            ->options(
                                \App\Models\GradoAcademico::where('activo', true)
                                    ->pluck('nombre', 'id')
                                    ->toArray()
                            )
                            ->required()
                            ->placeholder('Selecciona un grado'),

                        Select::make('grado_academico_jefe_id')
                            ->label('Grado académico (Jefe)')
                            ->options(
                                \App\Models\GradoAcademico::where('activo', true)
                                    ->pluck('nombre', 'id')
                                    ->toArray()
                            )
                            ->required()
                            ->placeholder('Selecciona un grado'),

                        Select::make('horario_id')
                            ->label('Horario')
                            ->options(
                                \App\Models\Horario::with('turno')
                                    ->get()
                                    ->mapWithKeys(function ($horario) {
                                        $turno = $horario->turno ? $horario->turno->nombre : 'Sin turno';
                                        return [$horario->id => $turno . ' ' . $horario->hora_inicio . ' - ' . $horario->hora_fin];
                                    })
                                    ->toArray()
                            )
                            ->required()
                            ->placeholder('Selecciona un horario'),

                        TextInput::make('nombre_persona_carta')
                            ->label('Nombre de la persona (Carta)')
                            ->required()
                            ->maxLength(255),

                        TextInput::make('cargo_persona_carta')
                            ->label('Cargo de la persona (Carta)')
                            ->required()
                            ->maxLength(255),

                        TextInput::make('nombre_jefe_inmediato')
                            ->label('Nombre del jefe inmediato')
                            ->required()
                            ->maxLength(255),

                        TextInput::make('cargo_jefe_inmediato')
                            ->label('Cargo del jefe inmediato')
                            ->required()
                            ->maxLength(255),

                        TextInput::make('area_asignada')
                            ->label('Área asignada')
                            ->required()
                            ->maxLength(255),

                        TextInput::make('apoyo_estudiante')
                            ->label('Apoyo al estudiante')
                            ->maxLength(255)
                            ->helperText('Ej: Equipo de cómputo, material, etc.'),
                    ])
                    ->action(function (array $data, $record) {
                        $servicioSocial = \App\Models\ServicioSocial::create([
                            'user_id' => $record->id,
                            'empresa_id' => $data['empresa_id'],
                            'fecha_inicio' => $data['fecha_inicio'],
                            'fecha_limite_primer_informe' => $data['fecha_limite_primer_informe'],
                            'fecha_limite_segundo_informe' => $data['fecha_limite_segundo_informe'],
                            'grado_academico_id' => $data['grado_academico_id'],
                            'grado_academico_jefe_id' => $data['grado_academico_jefe_id'],
                            'horario_id' => $data['horario_id'],
                            'nombre_persona_carta' => $data['nombre_persona_carta'],
                            'cargo_persona_carta' => $data['cargo_persona_carta'],
                            'nombre_jefe_inmediato' => $data['nombre_jefe_inmediato'],
                            'cargo_jefe_inmediato' => $data['cargo_jefe_inmediato'],
                            'area_asignada' => $data['area_asignada'],
                            'apoyo_estudiante' => $data['apoyo_estudiante'] ?? null,
                            'estatus' => 'pendiente',
                        ]);

                        $record->update([
                            'estatus_servicio_social' => 'pendiente'
                        ]);

                        Notification::make()
                            ->title('✅ Solicitud creada')
                            ->body("Se ha creado la solicitud de Servicio Social para {$record->name}")
                            ->success()
                            ->send();
                    })
                    ->modalHeading('Solicitar Servicio Social')
                    ->modalDescription('Completa los datos para crear la solicitud de Servicio Social')
                    ->modalSubmitActionLabel('Crear solicitud')
                    ->modalCancelActionLabel('Cancelar')
                    ->modalWidth('2xl'),

                Action::make('ver')
                    ->label('Ver')
                    ->icon('heroicon-o-eye')
                    ->color('info')
                    ->url(fn ($record) => route('filament.admin.resources.servicio-socials.view', $record)),

                Action::make('editar')
                    ->label('Editar SS')
                    ->icon('heroicon-o-pencil-square')
                    ->color('warning')
                    ->visible(fn ($record) => $record->servicioSocial)
                    ->url(fn ($record) => route('filament.admin.resources.servicio-socials.edit', $record->servicioSocial)),
            ])
            ->defaultSort('name')
            ->striped()
            ->paginated([25, 50, 100])
            ->emptyStateHeading('No hay alumnos registrados')
            ->emptyStateDescription('Aún no hay alumnos en el sistema. Importa alumnos desde el módulo de Importación Masiva.');
    }
    
    protected static function getDaysColor($record): string
    {
        if (!$record || !$record->fecha_limite_segundo_informe) {
            return 'gray';
        }
        
        $dias = Carbon::now()->diffInDays($record->fecha_limite_segundo_informe);
        
        if ($dias <= 7) {
            return 'danger';
        }
        
        if ($dias <= 15) {
            return 'warning';
        }
        
        return 'success';
    }
}