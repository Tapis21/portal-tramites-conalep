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
                    ->where(function ($query) {
                        $query->whereHas('periodos', function ($q) {
                            $q->where('activo', true);
                        })->orWhereDoesntHave('periodos');
                    })
                    ->with('servicioSocial')
                    ->with('periodos')
            )
            ->columns([
                TextColumn::make('name')
                    ->label('Estudiante')
                    ->searchable()
                    ->sortable()
                    ->formatStateUsing(fn ($record) => $record->name . ' ' . $record->apellidos)
                    ->extraAttributes(fn ($record) => [
                        'style' => !$record->periodos()->exists()
                            ? 'background-color: #fef9c3;'
                            : '',
                    ])
                    ->tooltip(fn ($record) => !$record->periodos()->exists()
                        ? '⚠️ Este usuario no tiene periodo asignado'
                        : ''
                    ),

                TextColumn::make('matricula')
                    ->label('Matrícula')
                    ->searchable()
                    ->sortable()
                    ->extraAttributes(fn ($record) => [
                        'style' => !$record->periodos()->exists()
                            ? 'background-color: #fef9c3;'
                            : '',
                    ]),

                TextColumn::make('grupo')
                    ->label('Grupo')
                    ->searchable()
                    ->sortable()
                    ->extraAttributes(fn ($record) => [
                        'style' => !$record->periodos()->exists()
                            ? 'background-color: #fef9c3;'
                            : '',
                    ]),

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
                    })
                    ->extraAttributes(fn ($record) => [
                        'style' => !$record->periodos()->exists()
                            ? 'background-color: #fef9c3;'
                            : '',
                    ]),

                TextColumn::make('servicioSocial.empresa.nombre')
                    ->label('Empresa')
                    ->searchable()
                    ->sortable()
                    ->placeholder('—')
                    ->extraAttributes(fn ($record) => [
                        'style' => !$record->periodos()->exists()
                            ? 'background-color: #fef9c3;'
                            : '',
                    ]),

                TextColumn::make('servicioSocial.fecha_inicio')
                    ->label('Inicio')
                    ->date('d/m/Y')
                    ->sortable()
                    ->placeholder('—')
                    ->extraAttributes(fn ($record) => [
                        'style' => !$record->periodos()->exists()
                            ? 'background-color: #fef9c3;'
                            : '',
                    ]),

                TextColumn::make('servicioSocial.fecha_limite_segundo_informe')
                    ->label('Finaliza')
                    ->date('d/m/Y')
                    ->sortable()
                    ->placeholder('—')
                    ->color(fn ($record) => $record->servicioSocial ? self::getDaysColor($record->servicioSocial) : 'gray')
                    ->extraAttributes(fn ($record) => [
                        'style' => !$record->periodos()->exists()
                            ? 'background-color: #fef9c3;'
                            : '',
                    ]),

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
                    ->formatStateUsing(fn ($state) => $state !== null ? number_format($state, 2) . ' días' : '—')
                    ->extraAttributes(fn ($record) => [
                        'style' => !$record->periodos()->exists()
                            ? 'background-color: #fef9c3;'
                            : '',
                    ]),
            ])
            ->actions([
                Action::make('solicitar_ss')
                    ->label('Solicitar SS')
                    ->icon('heroicon-o-plus-circle')
                    ->color('success')
                    ->visible(fn ($record) => !$record->servicioSocial)
                    ->modalHeading('Solicitar Servicio Social')
                    ->modalDescription('Completa los datos para crear la solicitud de Servicio Social')
                    ->modalSubmitActionLabel('Crear solicitud')
                    ->modalCancelActionLabel('Cancelar')
                    ->modalWidth('4xl')
                    ->form([
                        Select::make('empresa_id')
                            ->label('Institución / Empresa')
                            ->options(
                                \App\Models\Empresa::where('activo', true)
                                    ->where('servicio_social', true)
                                    ->orderBy('nombre')
                                    ->pluck('nombre', 'id')
                                    ->toArray()
                            )
                            ->required()
                            ->placeholder('— SELECCIONA UNA EMPRESA —')
                            ->searchable()
                            ->helperText('Selecciona la empresa, institución u organismo donde realizará el Servicio Social'),

                        Select::make('horario_id')
                            ->label('Horario de servicio')
                            ->options(function ($get, $record) {
                                $user = $record;
                                $turnoId = $user->turno_id ?? null;
                                
                                if (!$turnoId) {
                                    return \App\Models\Horario::with('turno')
                                        ->get()
                                        ->mapWithKeys(function ($horario) {
                                            $turno = $horario->turno ? $horario->turno->nombre : 'Sin turno';
                                            return [$horario->id => $turno . ' ' . $horario->hora_inicio . ' - ' . $horario->hora_fin];
                                        })
                                        ->toArray();
                                }
                                
                                return \App\Models\Horario::with('turno')
                                    ->where('turno_id', $turnoId)
                                    ->get()
                                    ->mapWithKeys(function ($horario) {
                                        $turno = $horario->turno ? $horario->turno->nombre : 'Sin turno';
                                        return [$horario->id => $turno . ' ' . $horario->hora_inicio . ' - ' . $horario->hora_fin];
                                    })
                                    ->toArray();
                            })
                            ->required()
                            ->placeholder('— SELECCIONA UN HORARIO —')
                            ->helperText('Selecciona el horario en que realizará el Servicio Social (según tu turno)'),

                        DatePicker::make('fecha_inicio')
                            ->label('Fecha de inicio')
                            ->required()
                            ->default(now())
                            ->helperText('Fecha en que da inicio el Servicio Social')
                            ->reactive()
                            ->afterStateUpdated(function ($state, $set, $get) {
                                if ($state) {
                                    $fechaFinal = Carbon::parse($state)->addMonths(6);
                                    $fechaFinalActual = $get('fecha_finalizacion');
                                    if (!$fechaFinalActual || Carbon::parse($fechaFinalActual)->lt($fechaFinal)) {
                                        $set('fecha_finalizacion', $fechaFinal->format('Y-m-d'));
                                    }
                                }
                            }),

                        DatePicker::make('fecha_finalizacion')
                            ->label('Fecha de finalización')
                            ->required()
                            ->helperText('Fecha en que finaliza el Servicio Social (mínimo 6 meses después del inicio)')
                            ->minDate(function ($get) {
                                $inicio = $get('fecha_inicio');
                                if ($inicio) {
                                    return Carbon::parse($inicio)->addMonths(6);
                                }
                                return now()->addMonths(6);
                            })
                            ->reactive()
                            ->afterStateUpdated(function ($state, $set, $get) {
                                $inicio = $get('fecha_inicio');
                                if ($inicio && $state) {
                                    $fechaMinima = Carbon::parse($inicio)->addMonths(6);
                                    if (Carbon::parse($state)->lt($fechaMinima)) {
                                        $set('fecha_finalizacion', $fechaMinima->format('Y-m-d'));
                                    }
                                }
                            }),

                        Select::make('grado_academico_id')
                            ->label('Grado académico (Carta de presentación)')
                            ->options(
                                \App\Models\GradoAcademico::where('activo', true)
                                    ->orderBy('nombre')
                                    ->pluck('nombre', 'id')
                                    ->toArray()
                            )
                            ->required()
                            ->placeholder('— SELECCIONA UN GRADO —')
                            ->helperText('Grado académico de la persona que firmará la carta de presentación (Ej: Lic., Ing., Dr.)'),

                        TextInput::make('nombre_persona_carta')
                            ->label('Nombre completo (Carta de presentación)')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('Ej: Lic. Juan Carlos Pérez Ramírez')
                            ->helperText('Nombre completo de la persona que firmará la carta de presentación'),

                        TextInput::make('cargo_persona_carta')
                            ->label('Cargo / Puesto (Carta de presentación)')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('Ej: Director de Recursos Humanos')
                            ->helperText('Cargo o puesto que ocupa la persona que firmará la carta de presentación'),

                        TextInput::make('area_asignada')
                            ->label('Área / Departamento asignado')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('Ej: Departamento de Sistemas')
                            ->helperText('Área, departamento o división donde realizará el Servicio Social'),

                        Select::make('grado_academico_jefe_id')
                            ->label('Grado académico (Jefe inmediato)')
                            ->options(
                                \App\Models\GradoAcademico::where('activo', true)
                                    ->orderBy('nombre')
                                    ->pluck('nombre', 'id')
                                    ->toArray()
                            )
                            ->required()
                            ->placeholder('— SELECCIONA UN GRADO —')
                            ->helperText('Grado académico del jefe inmediato del estudiante (Ej: Lic., Ing., Dr.)'),

                        TextInput::make('nombre_jefe_inmediato')
                            ->label('Nombre completo (Jefe inmediato)')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('Ej: Ing. María Elena González Torres')
                            ->helperText('Nombre completo del jefe inmediato del estudiante'),

                        TextInput::make('cargo_jefe_inmediato')
                            ->label('Cargo / Puesto (Jefe inmediato)')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('Ej: Subdirector de Operaciones')
                            ->helperText('Cargo o puesto que ocupa el jefe inmediato del estudiante'),

                        TextInput::make('apoyo_estudiante')
                            ->label('Apoyo al estudiante')
                            ->maxLength(255)
                            ->placeholder('Ej: Económico, equipo de cómputo, material, transporte')
                            ->helperText('Describe el apoyo que recibirá el estudiante durante el Servicio Social (opcional)'),
                    ])
                    ->action(function (array $data, $record) {
                        $servicioSocial = \App\Models\ServicioSocial::create([
                            'user_id' => $record->id,
                            'empresa_id' => $data['empresa_id'],
                            'fecha_inicio' => $data['fecha_inicio'],
                            'fecha_limite_segundo_informe' => $data['fecha_finalizacion'], // ← Guarda en fecha_limite_segundo_informe
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
                    }),

                Action::make('ver')
                    ->label('Ver')
                    ->icon('heroicon-o-eye')
                    ->color('info')
                    ->url(fn ($record) => route('filament.admin.resources.servicio-socials.view', $record)),
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