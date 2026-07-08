<?php

namespace App\Filament\Resources\Practicas\Tables;

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

class PracticasTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->query(
                User::query()
                    ->with('practicas')
                    ->with('periodos')
            )
            ->columns([
                // ✅ DATOS DEL ESTUDIANTE
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

                // ✅ DATOS DE PRÁCTICAS (si existe)
                TextColumn::make('practicas.empresa.nombre')
                    ->label('Empresa')
                    ->searchable()
                    ->sortable()
                    ->placeholder('—'),

                TextColumn::make('practicas.fecha_inicio')
                    ->label('Inicio')
                    ->date('d/m/Y')
                    ->sortable()
                    ->placeholder('—'),

                TextColumn::make('practicas.fecha_limite_final')
                    ->label('Finaliza')
                    ->date('d/m/Y')
                    ->sortable()
                    ->placeholder('—')
                    ->color(fn ($record) => $record->practicas ? self::getDaysColor($record->practicas) : 'gray'),

                TextColumn::make('dias_restantes')
                    ->label('Días')
                    ->state(fn ($record) => $record->practicas ? Carbon::now()->diffInDays($record->practicas->fecha_limite_final) : null)
                    ->badge()
                    ->color(fn ($state) => match (true) {
                        $state === null => 'gray',
                        $state <= 7 => 'danger',
                        $state <= 15 => 'warning',
                        default => 'success',
                    })
                    ->formatStateUsing(fn ($state) => $state !== null ? number_format($state, 2) . ' días' : '—'),

                // ✅ ESTATUS DE PRÁCTICAS
                BadgeColumn::make('estatus_practicas')
                    ->label('Estatus')
                    ->colors([
                        'success' => 'liberado',
                        'warning' => 'pendiente_revision',
                        'info' => 'en_progreso',
                        'gray' => 'no_solicitado',
                    ])
                    ->formatStateUsing(function ($state, $record) {
                        if ($record->practicas) {
                            $estatus = $record->practicas->estatus;
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
                // ✅ ACCIÓN: SOLICITAR PRÁCTICAS (solo si no tiene)
                Action::make('solicitar_practicas')
                    ->label('Solicitar PP')
                    ->icon('heroicon-o-plus-circle')
                    ->color('success')
                    ->visible(fn ($record) => !$record->practicas)
                    ->form([
                        Select::make('empresa_id')
                            ->label('Empresa')
                            ->options(
                                \App\Models\Empresa::where('activo', true)
                                    ->where('practicas', true)
                                    ->pluck('nombre', 'id')
                                    ->toArray()
                            )
                            ->required()
                            ->searchable()
                            ->helperText('Selecciona la empresa donde realizará las Prácticas Profesionales'),

                        DatePicker::make('fecha_inicio')
                            ->label('Fecha de inicio')
                            ->required()
                            ->default(now())
                            ->helperText('Fecha en que inician las Prácticas'),

                        DatePicker::make('fecha_limite_parcial')
                            ->label('Límite primer informe')
                            ->required()
                            ->default(now()->addMonths(3))
                            ->helperText('Fecha límite para entregar el primer informe'),

                        DatePicker::make('fecha_limite_final')
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
                        $practica = \App\Models\Practica::create([
                            'user_id' => $record->id,
                            'empresa_id' => $data['empresa_id'],
                            'fecha_inicio' => $data['fecha_inicio'],
                            'fecha_limite_parcial' => $data['fecha_limite_parcial'],
                            'fecha_limite_final' => $data['fecha_limite_final'],
                            'horas_requeridas' => 360,
                            'horas_completadas' => 0,
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
                            'estatus_practicas' => 'pendiente'
                        ]);

                        Notification::make()
                            ->title('✅ Solicitud creada')
                            ->body("Se ha creado la solicitud de Prácticas Profesionales para {$record->name}")
                            ->success()
                            ->send();
                    })
                    ->modalHeading('Solicitar Prácticas Profesionales')
                    ->modalDescription('Completa los datos para crear la solicitud de Prácticas')
                    ->modalSubmitActionLabel('Crear solicitud')
                    ->modalCancelActionLabel('Cancelar')
                    ->modalWidth('2xl'),

                // ✅ ACCIÓN: VER ALUMNO
                Action::make('ver')
                    ->label('Ver')
                    ->icon('heroicon-o-eye')
                    ->color('info')
                    ->url(fn ($record) => route('filament.admin.resources.practicas.view', $record)),

                // ✅ ACCIÓN: EDITAR PRÁCTICAS (solo si tiene)
                Action::make('editar')
                    ->label('Editar PP')
                    ->icon('heroicon-o-pencil-square')
                    ->color('warning')
                    ->visible(fn ($record) => $record->practicas)
                    ->url(fn ($record) => route('filament.admin.resources.practicas.edit', $record->practicas)),
            ])
            ->defaultSort('name')
            ->striped()
            ->paginated([25, 50, 100])
            ->emptyStateHeading('No hay alumnos registrados')
            ->emptyStateDescription('Aún no hay alumnos en el sistema. Importa alumnos desde el módulo de Importación Masiva.');
    }
    
    protected static function getDaysColor($record): string
    {
        if (!$record || !$record->fecha_limite_final) {
            return 'gray';
        }
        
        $dias = Carbon::now()->diffInDays($record->fecha_limite_final);
        
        if ($dias <= 7) {
            return 'danger';
        }
        
        if ($dias <= 15) {
            return 'warning';
        }
        
        return 'success';
    }
}