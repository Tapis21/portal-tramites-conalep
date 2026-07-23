<?php

namespace App\Filament\Resources\ServicioSocials\Tables;

use Carbon\Carbon;
use Filament\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Tables\Filters\SelectFilter;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Hidden;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Storage;
use App\Models\User;
use App\Models\Periodo;

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
            // ✅ HABILITAR SELECCIÓN (CHECKBOXES)
            ->selectable()
            ->columns([
                // ================================================================
                // 👤 ESTUDIANTE
                // ================================================================
                TextColumn::make('name')
                    ->label('Estudiante')
                    ->searchable()
                    ->sortable()
                    ->formatStateUsing(function ($record) {
                        return $record->name . ' ' . $record->apellidos;
                    })
                    ->icon('heroicon-o-user')
                    ->iconColor('gray')
                    ->extraAttributes(function ($record) {
                        if (!$record->periodos()->exists()) {
                            return ['class' => 'sin-periodo'];
                        }
                        return [];
                    })
                    ->tooltip(function ($record) {
                        return !$record->periodos()->exists() ? '⚠️ Este usuario no tiene periodo asignado' : '';
                    }),

                // ================================================================
                // 🎓 MATRÍCULA
                // ================================================================
                TextColumn::make('matricula')
                    ->label('Matrícula')
                    ->searchable()
                    ->sortable()
                    ->copyable()
                    ->copyMessage('Matrícula copiada')
                    ->icon('heroicon-o-identification')
                    ->iconColor('gray')
                    ->extraAttributes(function ($record) {
                        if (!$record->periodos()->exists()) {
                            return ['class' => 'sin-periodo'];
                        }
                        return [];
                    }),

                // ================================================================
                // 📚 GRUPO
                // ================================================================
                TextColumn::make('grupo')
                    ->label('Grupo')
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color('gray')
                    ->extraAttributes(function ($record) {
                        if (!$record->periodos()->exists()) {
                            return ['class' => 'sin-periodo'];
                        }
                        return [];
                    }),

                // ================================================================
                // 📊 ESTATUS
                // ================================================================
                TextColumn::make('estatus_servicio_social')
                    ->label('Estatus')
                    ->badge()
                    ->color(function ($state, $record) {
                        $estatus = $record->servicioSocial?->estatus ?? 'no_solicitado';
                        return match ($estatus) {
                            'liberado' => 'success',
                            'pendiente_revision' => 'warning',
                            'en_progreso' => 'info',
                            'pendiente' => 'warning',
                            default => 'gray',
                        };
                    })
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
                    ->icon(function ($record) {
                        $estatus = $record->servicioSocial?->estatus ?? 'no_solicitado';
                        return match ($estatus) {
                            'liberado' => 'heroicon-o-check-circle',
                            'pendiente_revision' => 'heroicon-o-clock',
                            'en_progreso' => 'heroicon-o-arrow-path',
                            'pendiente' => 'heroicon-o-clock',
                            default => 'heroicon-o-plus-circle',
                        };
                    })
                    ->iconColor(function ($record) {
                        $estatus = $record->servicioSocial?->estatus ?? 'no_solicitado';
                        return match ($estatus) {
                            'liberado' => 'success',
                            'pendiente_revision' => 'warning',
                            'en_progreso' => 'info',
                            'pendiente' => 'warning',
                            default => 'gray',
                        };
                    })
                    ->extraAttributes(function ($record) {
                        if (!$record->periodos()->exists()) {
                            return ['class' => 'sin-periodo'];
                        }
                        return [];
                    }),

                // ================================================================
                // 🏢 EMPRESA
                // ================================================================
                TextColumn::make('servicioSocial.empresa.nombre')
                    ->label('Empresa')
                    ->searchable()
                    ->sortable()
                    ->placeholder('—')
                    ->icon('heroicon-o-building-office')
                    ->iconColor('gray')
                    ->extraAttributes(function ($record) {
                        if (!$record->periodos()->exists()) {
                            return ['class' => 'sin-periodo'];
                        }
                        return [];
                    }),

                // ================================================================
                // 📅 INICIO
                // ================================================================
                TextColumn::make('servicioSocial.fecha_inicio')
                    ->label('Inicio')
                    ->date('d/m/Y')
                    ->sortable()
                    ->placeholder('—')
                    ->icon('heroicon-o-calendar')
                    ->iconColor('gray')
                    ->extraAttributes(function ($record) {
                        if (!$record->periodos()->exists()) {
                            return ['class' => 'sin-periodo'];
                        }
                        return [];
                    }),

                // ================================================================
                // 📅 FINALIZA
                // ================================================================
                TextColumn::make('servicioSocial.fecha_limite_segundo_informe')
                    ->label('Finaliza')
                    ->date('d/m/Y')
                    ->sortable()
                    ->placeholder('—')
                    ->icon('heroicon-o-calendar-days')
                    ->iconColor(function ($record) {
                        if (!$record->servicioSocial) return 'gray';
                        $dias = Carbon::now()->diffInDays($record->servicioSocial->fecha_limite_segundo_informe);
                        if ($dias <= 7) return 'danger';
                        if ($dias <= 15) return 'warning';
                        return 'success';
                    })
                    ->color(function ($record) {
                        if (!$record->servicioSocial) return 'gray';
                        $dias = Carbon::now()->diffInDays($record->servicioSocial->fecha_limite_segundo_informe);
                        if ($dias <= 7) return 'danger';
                        if ($dias <= 15) return 'warning';
                        return 'success';
                    })
                    ->extraAttributes(function ($record) {
                        if (!$record->periodos()->exists()) {
                            return ['class' => 'sin-periodo'];
                        }
                        if ($record->servicioSocial) {
                            $dias = Carbon::now()->diffInDays($record->servicioSocial->fecha_limite_segundo_informe);
                            if ($dias <= 7) {
                                return ['class' => 'por-vencer'];
                            } elseif ($dias < 0) {
                                return ['class' => 'vencido'];
                            }
                        }
                        return [];
                    }),

                // ================================================================
                // ⏱️ TIEMPO
                // ================================================================
                TextColumn::make('tiempo')
                    ->label('Tiempo')
                    ->state(function ($record) {
                        if (!$record->servicioSocial) {
                            return '—';
                        }
                        
                        if ($record->servicioSocial->estatus === 'liberado') {
                            return '✅ Finalizado';
                        }
                        
                        $dias = Carbon::now()->diffInDays($record->servicioSocial->fecha_limite_segundo_informe, false);
                        return intval($dias) . ' días';
                    })
                    ->badge()
                    ->color(function ($state, $record) {
                        if ($state === '—') return 'gray';
                        if ($state === '✅ Finalizado') return 'success';
                        
                        $dias = intval(preg_replace('/[^0-9-]/', '', $state));
                        
                        if ($dias <= 7 && $dias >= 0) return 'danger';
                        if ($dias <= 15 && $dias >= 0) return 'warning';
                        if ($dias < 0) return 'danger';
                        return 'success';
                    })
                    ->icon(function ($state, $record) {
                        if ($state === '—') return 'heroicon-o-minus-circle';
                        if ($state === '✅ Finalizado') return 'heroicon-o-check-circle';
                        return 'heroicon-o-clock';
                    })
                    ->extraAttributes(function ($record) {
                        if (!$record->periodos()->exists()) {
                            return ['class' => 'sin-periodo'];
                        }
                        return [];
                    }),
            ])
            // ================================================================
            // 🔍 FILTROS
            // ================================================================
            ->filters([
                SelectFilter::make('estatus_servicio_social')
                    ->label('Estatus de SS')
                    ->options([
                        'no_solicitado' => '📋 No solicitado',
                        'pendiente' => '⏳ Pendiente',
                        'en_progreso' => '🔄 En progreso',
                        'pendiente_revision' => '⚠️ En revisión',
                        'liberado' => '✅ Liberado',
                    ])
                    ->query(function ($query, array $data) {
                        if ($data['value'] === 'no_solicitado') {
                            $query->whereDoesntHave('servicioSocial');
                        } else {
                            $query->whereHas('servicioSocial', function ($q) use ($data) {
                                $q->where('estatus', $data['value']);
                            });
                        }
                    }),

                SelectFilter::make('generacion')
                    ->label('Generación')
                    ->options(function () {
                        return Periodo::orderBy('año_inicio', 'desc')
                            ->limit(3)
                            ->pluck('nombre', 'id')
                            ->toArray();
                    })
                    ->query(function ($query, array $data) {
                        if (!empty($data['value'])) {
                            $query->whereHas('periodos', function ($q) use ($data) {
                                $q->where('periodo_id', $data['value']);
                            });
                        }
                    }),

                SelectFilter::make('sin_periodo')
                    ->label('⚠️ Sin periodo')
                    ->options([
                        '1' => 'Mostrar sin periodo',
                    ])
                    ->query(function ($query, array $data) {
                        if ($data['value'] === '1') {
                            $query->whereDoesntHave('periodos');
                        }
                    }),
            ])
            // ================================================================
            // 🛠️ ACCIONES
            // ================================================================
            ->actions([
                Action::make('solicitar_ss')
                    ->label('Solicitar SS')
                    ->icon('heroicon-o-plus-circle')
                    ->color('success')
                    ->visible(function ($record) {
                        return !$record->servicioSocial;
                    })
                    ->extraAttributes(['class' => 'action-btn success-btn'])
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
                            ->helperText('Selecciona el horario en que realizará el Servicio Social (según tu turno)'),

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
                                    
                                    $fechaTerminacion = $fechaInicio->copy()->addMonths(6);
                                    $fechaTerminacion = self::ajustarFinDeSemana($fechaTerminacion);
                                    
                                    $set('fecha_terminacion', $fechaTerminacion->format('Y-m-d'));
                                    self::calcularPrimerInforme($fechaInicio, $fechaTerminacion, $set);
                                }
                            }),

                        DatePicker::make('fecha_terminacion')
                            ->label('Fecha de terminación')
                            ->required()
                            ->helperText('Fecha en que finaliza el Servicio Social (Se ajusta automáticamente si cae en fin de semana)')
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

                        Hidden::make('fecha_limite_primer_informe'),

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
                            ->helperText('Describe el apoyo que recibirá el estudiante durante el Servicio Social (opcional)'),
                    ])
                    ->action(function (array $data, $record) {
                        $servicioSocial = \App\Models\ServicioSocial::create([
                            'user_id' => $record->id,
                            'empresa_id' => $data['empresa_id'],
                            'fecha_inicio' => $data['fecha_inicio'],
                            'fecha_limite_primer_informe' => $data['fecha_limite_primer_informe'],
                            'fecha_limite_segundo_informe' => $data['fecha_terminacion'],
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

                Action::make('eliminar_solicitud')
                    ->label('Eliminar solicitud')
                    ->icon('heroicon-o-trash')
                    ->color('danger')
                    ->visible(function ($record) {
                        return $record->servicioSocial && in_array($record->servicioSocial->estatus, ['pendiente', 'en_progreso']);
                    })
                    ->extraAttributes(['class' => 'action-btn danger-btn'])
                    ->modalHeading('Eliminar solicitud de Servicio Social')
                    ->modalDescription('¿Estás seguro de que deseas eliminar esta solicitud? Se eliminarán también todos los documentos y comentarios asociados. Esta acción no se puede deshacer.')
                    ->modalSubmitActionLabel('Sí, eliminar todo')
                    ->modalCancelActionLabel('Cancelar')
                    ->action(function ($record) {
                        $servicioSocial = $record->servicioSocial;
                        $nombreEstudiante = $record->name;
                        
                        $documentos = $servicioSocial->documentos ?? collect();
                        
                        foreach ($documentos as $documento) {
                            $documento->comentarios()->delete();
                            
                            if ($documento->archivo_pdf && Storage::exists($documento->archivo_pdf)) {
                                Storage::delete($documento->archivo_pdf);
                            }
                            
                            $documento->delete();
                        }
                        
                        $servicioSocial->comentarios()->delete();
                        $servicioSocial->delete();
                        
                        $record->update([
                            'estatus_servicio_social' => 'no_solicitado'
                        ]);
                        
                        Notification::make()
                            ->title('🗑️ Solicitud eliminada')
                            ->body("Se ha eliminado la solicitud de Servicio Social para {$nombreEstudiante}")
                            ->success()
                            ->send();
                    }),

                Action::make('ver')
                    ->label('Ver')
                    ->icon('heroicon-o-eye')
                    ->color('info')
                    ->extraAttributes(['class' => 'action-btn info-btn'])
                    ->url(function ($record) {
                        return route('filament.admin.resources.servicio-socials.view', $record);
                    }),
            ])
            // ================================================================
            // 📋 BULK ACTIONS (ELIMINAR SOLICITUDES SELECCIONADAS)
            // ================================================================
            ->bulkActions([
                Action::make('eliminar_solicitudes_seleccionadas')
                    ->label('Eliminar solicitudes seleccionadas')
                    ->icon('heroicon-o-trash')
                    ->color('danger')
                    ->extraAttributes(['class' => 'bulk-action-btn'])
                    ->requiresConfirmation()
                    ->modalHeading('Eliminar solicitudes de SS')
                    ->modalDescription('¿Estás seguro de eliminar las solicitudes de Servicio Social de los alumnos seleccionados? Esta acción no se puede deshacer.')
                    ->modalSubmitActionLabel('Sí, eliminar todo')
                    ->modalCancelActionLabel('Cancelar')
                    ->action(function ($records) {
                        $count = 0;
                        foreach ($records as $user) {
                            if ($user->servicioSocial) {
                                $servicioSocial = $user->servicioSocial;
                                $documentos = $servicioSocial->documentos ?? collect();
                                
                                foreach ($documentos as $documento) {
                                    $documento->comentarios()->delete();
                                    if ($documento->archivo_pdf && Storage::exists($documento->archivo_pdf)) {
                                        Storage::delete($documento->archivo_pdf);
                                    }
                                    $documento->delete();
                                }
                                
                                $servicioSocial->comentarios()->delete();
                                $servicioSocial->delete();
                                
                                $user->update(['estatus_servicio_social' => 'no_solicitado']);
                                $count++;
                            }
                        }
                        
                        Notification::make()
                            ->title('🗑️ Solicitudes eliminadas')
                            ->body("Se eliminaron {$count} solicitudes de Servicio Social correctamente.")
                            ->success()
                            ->send();
                    }),
            ])
            // ================================================================
            // 📊 CONFIGURACIÓN DE LA TABLA
            // ================================================================
            ->defaultSort('name')
            ->striped()
            ->paginated([25, 50, 100])
            ->emptyStateHeading('No hay alumnos registrados')
            ->emptyStateDescription('Aún no hay alumnos en el sistema. Importa alumnos desde el módulo de Importación Masiva.')
            ->emptyStateIcon('heroicon-o-user-group');
    }

    // ================================================================
    // 🔧 FUNCIONES AUXILIARES
    // ================================================================
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
        
        $set('fecha_limite_primer_informe', $fechaPrimerInforme->format('Y-m-d'));
    }
}