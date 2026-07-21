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
use Filament\Forms\Components\Hidden;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Storage;
use App\Models\User;

class PracticasTable
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
                    ->with('practicas')
                    ->with('periodos')
                    ->with('servicioSocial')
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
                    })
                    ->extraAttributes(fn ($record) => [
                        'style' => !$record->periodos()->exists()
                            ? 'background-color: #fef9c3;'
                            : '',
                    ]),

                TextColumn::make('practicas.empresa.nombre')
                    ->label('Empresa')
                    ->searchable()
                    ->sortable()
                    ->placeholder('—')
                    ->extraAttributes(fn ($record) => [
                        'style' => !$record->periodos()->exists()
                            ? 'background-color: #fef9c3;'
                            : '',
                    ]),

                TextColumn::make('practicas.fecha_inicio')
                    ->label('Inicio')
                    ->date('d/m/Y')
                    ->sortable()
                    ->placeholder('—')
                    ->extraAttributes(fn ($record) => [
                        'style' => !$record->periodos()->exists()
                            ? 'background-color: #fef9c3;'
                            : '',
                    ]),

                TextColumn::make('practicas.fecha_limite_final')
                    ->label('Finaliza')
                    ->date('d/m/Y')
                    ->sortable()
                    ->placeholder('—')
                    ->color(function ($record) {
                        if (!$record->practicas) {
                            return 'gray';
                        }
                        $dias = Carbon::now()->diffInDays($record->practicas->fecha_limite_final);
                        if ($dias <= 7) {
                            return 'danger';
                        }
                        if ($dias <= 15) {
                            return 'warning';
                        }
                        return 'success';
                    })
                    ->extraAttributes(fn ($record) => [
                        'style' => !$record->periodos()->exists()
                            ? 'background-color: #fef9c3;'
                            : '',
                    ]),

                TextColumn::make('tiempo')
                    ->label('Tiempo')
                    ->state(function ($record) {
                        if (!$record->practicas) {
                            return '—';
                        }
                        
                        if ($record->practicas->estatus === 'liberado') {
                            return '✅ Finalizado';
                        }
                        
                        $dias = Carbon::now()->diffInDays($record->practicas->fecha_limite_final, false);
                        return intval($dias) . ' días';
                    })
                    ->badge()
                    ->color(function ($state, $record) {
                        if ($state === '—') {
                            return 'gray';
                        }
                        if ($state === '✅ Finalizado') {
                            return 'success';
                        }
                        
                        $dias = intval(preg_replace('/[^0-9-]/', '', $state));
                        
                        if ($dias <= 7 && $dias >= 0) {
                            return 'danger';
                        } elseif ($dias <= 15 && $dias >= 0) {
                            return 'warning';
                        } elseif ($dias < 0) {
                            return 'danger';
                        } else {
                            return 'success';
                        }
                    })
                    ->extraAttributes(fn ($record) => [
                        'style' => !$record->periodos()->exists()
                            ? 'background-color: #fef9c3;'
                            : '',
                    ]),
            ])
            ->actions([
                Action::make('solicitar_practicas')
                    ->label('Solicitar PP')
                    ->icon('heroicon-o-plus-circle')
                    ->color('success')
                    ->visible(function ($record) {
                        if ($record->practicas) {
                            return false;
                        }

                        $ss = $record->servicioSocial;
                        if (!$ss || $ss->estatus !== 'liberado') {
                            return false;
                        }

                        return true;
                    })
                    ->tooltip(function ($record) {
                        $ss = $record->servicioSocial;
                        if (!$ss) {
                            return '⚠️ El estudiante no ha solicitado Servicio Social. Debe liberarlo primero.';
                        }
                        if ($ss->estatus !== 'liberado') {
                            $estatusLabel = match ($ss->estatus) {
                                'en_progreso' => 'En progreso',
                                'pendiente' => 'Pendiente',
                                'no_solicitado' => 'No solicitado',
                                default => $ss->estatus,
                            };
                            return '⚠️ El estudiante debe liberar su Servicio Social primero. Estatus actual: ' . $estatusLabel;
                        }
                        return null;
                    })
                    ->modalHeading('Solicitar Prácticas Profesionales')
                    ->modalDescription('Completa los datos para crear la solicitud de Prácticas')
                    ->modalSubmitActionLabel('Crear solicitud')
                    ->modalCancelActionLabel('Cancelar')
                    ->modalWidth('4xl')
                    ->form([
                        Select::make('empresa_id')
                            ->label('Institución / Empresa')
                            ->options(
                                \App\Models\Empresa::where('activo', true)
                                    ->where('practicas', true)
                                    ->orderBy('nombre')
                                    ->pluck('nombre', 'id')
                                    ->toArray()
                            )
                            ->required()
                            ->placeholder('— SELECCIONA UNA EMPRESA —')
                            ->searchable()
                            ->helperText('Selecciona la empresa, institución u organismo donde realizará las Prácticas Profesionales'),

                        Select::make('horario_id')
                            ->label('Horario de prácticas')
                            ->options(function ($get, $record) {
                                $user = $record;
                                $turnoId = $user->turno_id ?? null;
                                
                                $horarios = \App\Models\Horario::with('turno');
                                
                                if ($turnoId) {
                                    $horarios->where('turno_id', $turnoId);
                                }
                                
                                return $horarios->get()
                                    ->mapWithKeys(function ($horario) {
                                        return [$horario->id => $horario->hora_inicio . ' - ' . $horario->hora_fin];
                                    })
                                    ->toArray();
                            })
                            ->required()
                            ->placeholder('— SELECCIONA UN HORARIO —')
                            ->helperText('Selecciona el horario en que realizará las Prácticas (según tu turno)'),

                        DatePicker::make('fecha_inicio')
                            ->label('Fecha de inicio')
                            ->required()
                            ->default(now())
                            ->helperText('Selecciona la fecha de inicio. Si es sábado se ajusta a viernes, si es domingo a lunes.')
                            ->reactive()
                            ->afterStateUpdated(function ($state, $set, $get) {
                                if ($state) {
                                    $fechaInicio = Carbon::parse($state);
                                    $fechaInicio = self::ajustarInicio($fechaInicio);
                                    $set('fecha_inicio', $fechaInicio->format('Y-m-d'));
                                    
                                    $fechaTerminacion = $fechaInicio->copy()->addMonths(4);
                                    $fechaTerminacion = self::ajustarFinDeSemana($fechaTerminacion);
                                    
                                    $set('fecha_terminacion', $fechaTerminacion->format('Y-m-d'));
                                    self::calcularPrimerInforme($fechaInicio, $fechaTerminacion, $set);
                                }
                            }),

                        DatePicker::make('fecha_terminacion')
                            ->label('Fecha de terminación')
                            ->required()
                            ->helperText('Fecha en que finalizan las Prácticas Profesionales (Se ajusta automáticamente si cae en fin de semana)')
                            ->reactive()
                            ->afterStateUpdated(function ($state, $set, $get) {
                                $fechaInicio = $get('fecha_inicio');
                                if ($fechaInicio && $state) {
                                    $fechaTerminacion = Carbon::parse($state);
                                    $fechaTerminacion = self::ajustarFinDeSemana($fechaTerminacion);
                                    $set('fecha_terminacion', $fechaTerminacion->format('Y-m-d'));
                                    self::calcularPrimerInforme($fechaInicio, $fechaTerminacion, $set);
                                }
                            }),

                        Hidden::make('fecha_limite_parcial'),

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
                            ->helperText('Grado académico de la persona que firmará la carta de presentación'),

                        TextInput::make('nombre_persona_carta')
                            ->label('Nombre completo (Carta de presentación)')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('Ej: Juan Carlos Pérez Ramírez')
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
                            ->helperText('Área, departamento o división donde realizará las Prácticas'),

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
                            ->helperText('Grado académico del jefe inmediato del estudiante'),

                        TextInput::make('nombre_jefe_inmediato')
                            ->label('Nombre completo (Jefe inmediato)')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('Ej: María Elena González Torres')
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
                            ->helperText('Describe el apoyo que recibirá el estudiante durante las Prácticas (opcional)'),
                    ])
                    ->action(function (array $data, $record) {
                        $practica = \App\Models\Practica::create([
                            'user_id' => $record->id,
                            'empresa_id' => $data['empresa_id'],
                            'fecha_inicio' => $data['fecha_inicio'],
                            'fecha_limite_parcial' => $data['fecha_limite_parcial'],
                            'fecha_limite_final' => $data['fecha_terminacion'],
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
                    }),

                Action::make('eliminar_solicitud')
                    ->label('Eliminar solicitud')
                    ->icon('heroicon-o-trash')
                    ->color('danger')
                    ->visible(fn ($record) => $record->practicas && in_array($record->practicas->estatus, ['pendiente', 'en_progreso']))
                    ->modalHeading('Eliminar solicitud de Prácticas Profesionales')
                    ->modalDescription('¿Estás seguro de que deseas eliminar esta solicitud? Se eliminarán también todos los documentos y comentarios asociados. Esta acción no se puede deshacer.')
                    ->modalSubmitActionLabel('Sí, eliminar todo')
                    ->modalCancelActionLabel('Cancelar')
                    ->action(function ($record) {
                        $practicas = $record->practicas;
                        $nombreEstudiante = $record->name;
                        
                        // 1. Obtener documentos de las prácticas
                        $documentos = $practicas->documentos ?? collect();
                        
                        // 2. Eliminar documentos y sus comentarios
                        foreach ($documentos as $documento) {
                            $documento->comentarios()->delete();
                            
                            if ($documento->archivo_pdf && Storage::exists($documento->archivo_pdf)) {
                                Storage::delete($documento->archivo_pdf);
                            }
                            
                            $documento->delete();
                        }
                        
                        // 3. Eliminar comentarios directos de las prácticas
                        $practicas->comentarios()->delete();
                        
                        // 4. Eliminar las prácticas
                        $practicas->delete();
                        
                        // 5. Actualizar estatus del usuario
                        $record->update([
                            'estatus_practicas' => 'no_solicitado'
                        ]);
                        
                        Notification::make()
                            ->title('🗑️ Solicitud eliminada')
                            ->body("Se ha eliminado la solicitud de Prácticas Profesionales para {$nombreEstudiante}")
                            ->success()
                            ->send();
                    }),

                Action::make('ver')
                    ->label('Ver')
                    ->icon('heroicon-o-eye')
                    ->color('info')
                    ->url(fn ($record) => route('filament.admin.resources.practicas.view', $record)),
            ])
            ->defaultSort('name')
            ->striped()
            ->paginated([25, 50, 100])
            ->emptyStateHeading('No hay alumnos registrados')
            ->emptyStateDescription('Aún no hay alumnos en el sistema. Importa alumnos desde el módulo de Importación Masiva.');
    }

    protected static function ajustarInicio(Carbon $fecha): Carbon
    {
        $diaSemana = $fecha->dayOfWeek;
        if ($diaSemana === 6) {
            return $fecha->subDays(1);
        } elseif ($diaSemana === 0) {
            return $fecha->addDays(1);
        }
        return $fecha;
    }

    protected static function ajustarFinDeSemana(Carbon $fecha): Carbon
    {
        $diaSemana = $fecha->dayOfWeek;
        if ($diaSemana === 6) {
            return $fecha->addDays(2);
        } elseif ($diaSemana === 0) {
            return $fecha->addDays(1);
        }
        return $fecha;
    }

    protected static function calcularPrimerInforme($fechaInicio, $fechaTerminacion, $set): void
    {
        $inicio = Carbon::parse($fechaInicio);
        $terminacion = Carbon::parse($fechaTerminacion);
        
        $diasTotales = $inicio->diffInDays($terminacion);
        $diasMitad = intdiv($diasTotales, 2);
        
        $fechaPrimerInforme = $inicio->copy()->addDays($diasMitad);
        $fechaPrimerInforme = self::ajustarFinDeSemana($fechaPrimerInforme);
        
        $set('fecha_limite_parcial', $fechaPrimerInforme->format('Y-m-d'));
    }
}