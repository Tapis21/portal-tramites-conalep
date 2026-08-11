<?php

namespace App\Filament\Resources\ServicioSocials\Pages;

use App\Filament\Resources\ServicioSocials\ServicioSocialResource;
use App\Models\Documento;
use App\Models\Comentario;
use App\Models\ServicioSocial;
use App\Models\User;
use Filament\Actions;
use Filament\Actions\Action;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Select;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\HtmlString;

class ViewServicioSocial extends ViewRecord implements HasTable
{
    use InteractsWithTable;

    protected static string $resource = ServicioSocialResource::class;
    protected string $view = 'filament.modals.view-servicio-social';

    public $comentario_contenido = '';
    public $nuevo_estatus = '';
    public $comentario_estado = '';
    public $nuevo_estatus_estudiante = '';
    public $comentario_estatus_estudiante = '';

    /**
     * ✅ Obtener el ServicioSocial del usuario actual
     */
    protected function getServicioSocial()
    {
        return $this->record->servicioSocial;
    }

    public function getTitle(): string
    {
        $nombre = $this->record->name . ' ' . $this->record->apellidos;
        return "Revisando a: {$nombre}";
    }

    public function infolist(Schema $schema): Schema
    {
        return $schema
            ->schema([
                // ================================================================
                // 👤 DATOS DEL ESTUDIANTE
                // ================================================================
                Section::make('Datos del Estudiante')
                    ->icon('heroicon-o-user-group')
                    ->extraAttributes(['class' => 'info-section info-section-student'])
                    ->schema([
                        TextEntry::make('name')
                            ->label('Nombre completo')
                            ->formatStateUsing(fn () => $this->record->name . ' ' . $this->record->apellidos)
                            ->icon('heroicon-o-user'),
                        TextEntry::make('matricula')
                            ->label('Matrícula')
                            ->icon('heroicon-o-identification'),
                        TextEntry::make('carrera')
                            ->label('Carrera')
                            ->icon('heroicon-o-academic-cap'),
                        TextEntry::make('semestre')
                            ->label('Semestre')
                            ->formatStateUsing(fn ($state) => $state . '° Semestre')
                            ->icon('heroicon-o-numbered-list'),
                        TextEntry::make('grupo')
                            ->label('Grupo')
                            ->placeholder('No asignado')
                            ->icon('heroicon-o-users'),
                        TextEntry::make('turno.nombre')
                            ->label('Turno')
                            ->default(fn () => $this->record->turno?->nombre ?? 'No definido')
                            ->icon('heroicon-o-clock'),
                    ])
                    ->columns(3),

                // ================================================================
                // 🏢 DATOS DE LA EMPRESA
                // ================================================================
                Section::make('Datos de la Empresa')
                    ->icon('heroicon-o-building-office-2')
                    ->extraAttributes(['class' => 'info-section info-section-company'])
                    ->visible(fn () => $this->getServicioSocial() !== null)
                    ->schema([
                        TextEntry::make('servicioSocial.empresa.nombre')
                            ->label('Empresa')
                            ->default(fn () => $this->getServicioSocial()?->empresa?->nombre ?? 'No especificada')
                            ->icon('heroicon-o-building-office'),
                        TextEntry::make('servicioSocial.area_asignada')
                            ->label('Área asignada')
                            ->default(fn () => $this->getServicioSocial()?->area_asignada ?? 'No especificada')
                            ->icon('heroicon-o-map-pin'),
                    ])
                    ->columns(2),

                // ================================================================
                // ⚠️ SIN SOLICITUD
                // ================================================================
                Section::make('Sin Solicitud de Servicio Social')
                    ->icon('heroicon-o-information-circle')
                    ->extraAttributes(['class' => 'info-section info-section-empty'])
                    ->visible(fn () => $this->getServicioSocial() === null)
                    ->schema([
                        TextEntry::make('sin_solicitud')
                            ->label('')
                            ->default('Este alumno aún no ha solicitado Servicio Social.')
                            ->icon('heroicon-o-exclamation-circle'),
                    ]),

                // ================================================================
                // 📅 FECHAS Y HORAS
                // ================================================================
                Section::make('Fechas y Horas')
                    ->icon('heroicon-o-calendar-days')
                    ->extraAttributes(['class' => 'info-section info-section-dates'])
                    ->visible(fn () => $this->getServicioSocial() !== null)
                    ->schema([
                        TextEntry::make('servicioSocial.fecha_inicio')
                            ->label('Fecha de inicio')
                            ->date('d/m/Y')
                            ->default(fn () => $this->getServicioSocial()?->fecha_inicio)
                            ->icon('heroicon-o-calendar'),
                        TextEntry::make('servicioSocial.fecha_limite_segundo_informe')
                            ->label('Fecha de finalización')
                            ->date('d/m/Y')
                            ->default(fn () => $this->getServicioSocial()?->fecha_limite_segundo_informe)
                            ->icon('heroicon-o-calendar-days'),
                        TextEntry::make('servicioSocial.horario.hora_inicio')
                            ->label('Horario')
                            ->formatStateUsing(function () {
                                $horario = $this->getServicioSocial()?->horario;
                                return $horario ? $horario->hora_inicio . ' - ' . $horario->hora_fin : 'No definido';
                            })
                            ->icon('heroicon-o-clock'),
                        TextEntry::make('servicioSocial.fecha_limite_primer_informe')
                            ->label('Límite primer informe')
                            ->date('d/m/Y')
                            ->default(fn () => $this->getServicioSocial()?->fecha_limite_primer_informe)
                            ->icon('heroicon-o-calendar'),
                        TextEntry::make('servicioSocial.fecha_limite_segundo_informe')
                            ->label('Límite segundo informe')
                            ->date('d/m/Y')
                            ->default(fn () => $this->getServicioSocial()?->fecha_limite_segundo_informe)
                            ->icon('heroicon-o-calendar'),
                    ])
                    ->columns(3),

                // ================================================================
                // 📞 DATOS DE CONTACTO
                // ================================================================
                Section::make('Datos de Contacto')
                    ->icon('heroicon-o-phone')
                    ->extraAttributes(['class' => 'info-section info-section-contact'])
                    ->visible(fn () => $this->getServicioSocial() !== null)
                    ->schema([
                        TextEntry::make('servicioSocial.gradoAcademico.abreviatura')
                            ->label('Grado (Carta)')
                            ->default(fn () => $this->getServicioSocial()?->gradoAcademico?->abreviatura ?? 'No definido')
                            ->icon('heroicon-o-user-circle'),
                        TextEntry::make('servicioSocial.nombre_persona_carta')
                            ->label('Nombre de la persona')
                            ->default(fn () => $this->getServicioSocial()?->nombre_persona_carta)
                            ->icon('heroicon-o-user'),
                        TextEntry::make('servicioSocial.cargo_persona_carta')
                            ->label('Cargo de la persona')
                            ->default(fn () => $this->getServicioSocial()?->cargo_persona_carta)
                            ->icon('heroicon-o-briefcase'),
                        TextEntry::make('servicioSocial.gradoAcademicoJefe.abreviatura')
                            ->label('Grado (Jefe)')
                            ->default(fn () => $this->getServicioSocial()?->gradoAcademicoJefe?->abreviatura ?? 'No definido')
                            ->icon('heroicon-o-user-circle'),
                        TextEntry::make('servicioSocial.nombre_jefe_inmediato')
                            ->label('Nombre del jefe inmediato')
                            ->default(fn () => $this->getServicioSocial()?->nombre_jefe_inmediato)
                            ->icon('heroicon-o-user'),
                        TextEntry::make('servicioSocial.cargo_jefe_inmediato')
                            ->label('Cargo del jefe inmediato')
                            ->default(fn () => $this->getServicioSocial()?->cargo_jefe_inmediato)
                            ->icon('heroicon-o-briefcase'),
                    ])
                    ->columns(3),

                // ================================================================
                // ℹ️ INFORMACIÓN ADICIONAL
                // ================================================================
                Section::make('Información Adicional')
                    ->icon('heroicon-o-information-circle')
                    ->extraAttributes(['class' => 'info-section info-section-additional'])
                    ->visible(fn () => $this->getServicioSocial() !== null)
                    ->footerActions([
                        Actions\Action::make('cambiar_estatus_estudiante')
                            ->label('Cambiar Estatus')
                            ->icon('heroicon-o-pencil-square')
                            ->color('primary')
                            ->extraAttributes(['class' => 'action-btn primary-btn'])
                            ->visible(fn () => Auth::user()->role === 'admin')
                            ->form([
                                Select::make('nuevo_estatus_estudiante')
                                    ->label('Nuevo Estatus')
                                    ->options([
                                        'no_solicitado' => 'No solicitado',
                                        'pendiente' => 'Pendiente',
                                        'en_progreso' => 'En progreso',
                                        'liberado' => 'Liberado',
                                    ])
                                    ->default(fn ($record) => $this->getServicioSocial()?->estatus ?? 'no_solicitado')
                                    ->required(),
                                Textarea::make('comentario_estatus_estudiante')
                                    ->label('Comentario (opcional)')
                                    ->placeholder('Agrega un comentario sobre este cambio de estatus...')
                                    ->rows(3)
                                    ->maxLength(500),
                            ])
                            ->action(function (array $data) {
                                $servicioSocial = $this->getServicioSocial();
                                
                                if (!$servicioSocial) {
                                    Notification::make()
                                        ->title('Error')
                                        ->body('No se encontró el registro de Servicio Social.')
                                        ->danger()
                                        ->send();
                                    return;
                                }

                                if (empty($data['nuevo_estatus_estudiante'])) {
                                    Notification::make()
                                        ->title('Error')
                                        ->body('Debes seleccionar un estatus.')
                                        ->danger()
                                        ->send();
                                    return;
                                }

                                $estatusAnterior = $servicioSocial->estatus;
                                
                                $servicioSocial->update([
                                    'estatus' => $data['nuevo_estatus_estudiante']
                                ]);

                                $servicioSocial->user->update([
                                    'estatus_servicio_social' => $data['nuevo_estatus_estudiante']
                                ]);

                                if (!empty($data['comentario_estatus_estudiante'])) {
                                    $documento = Documento::where('user_id', $this->record->id)
                                        ->where('activo', true)
                                        ->whereHas('tipoDocumento', function($q) {
                                            $q->where('tramite', 'SS');
                                        })
                                        ->first();

                                    if (!$documento) {
                                        $documento = Documento::where('user_id', $this->record->id)
                                            ->whereHas('tipoDocumento', function($q) {
                                                $q->where('tramite', 'SS');
                                            })
                                            ->first();
                                    }

                                    if (!$documento) {
                                        $tipoDocumento = \App\Models\TipoDocumento::where('nombre', 'Solicitud de Servicio Social')
                                            ->where('tramite', 'SS')
                                            ->first();
                                        
                                        if ($tipoDocumento) {
                                            $documento = Documento::create([
                                                'user_id' => $this->record->id,
                                                'tipo_documento_id' => $tipoDocumento->id,
                                                'archivo_pdf' => null,
                                                'estatus' => 'pendiente',
                                                'activo' => true,
                                            ]);
                                        }
                                    }

                                    if ($documento) {
                                        Comentario::create([
                                            'contenido' => 'Cambio de estatus de "' . $estatusAnterior . '" a "' . $data['nuevo_estatus_estudiante'] . '": ' . $data['comentario_estatus_estudiante'],
                                            'tipo' => 'admin',
                                            'comentable_type' => 'App\\Models\\Documento',
                                            'comentable_id' => $documento->id,
                                            'user_id' => Auth::id(),
                                            'leido' => false
                                        ]);
                                    } else {
                                        Notification::make()
                                            ->title('Advertencia')
                                            ->body('No se pudo asociar el comentario a un documento específico, pero el estatus se actualizó correctamente.')
                                            ->warning()
                                            ->send();
                                    }
                                }

                                Notification::make()
                                    ->title('Estatus actualizado')
                                    ->body('El estatus del estudiante ha sido cambiado correctamente.')
                                    ->success()
                                    ->send();

                                $this->dispatch('refresh-table');
                            })
                            ->modalSubmitActionLabel('Actualizar Estatus')
                            ->modalCancelActionLabel('Cancelar')
                            ->modalWidth('md'),
                    ])
                    ->schema([
                        TextEntry::make('servicioSocial.apoyo_estudiante')
                            ->label('Apoyo al estudiante')
                            ->default(fn () => $this->getServicioSocial()?->apoyo_estudiante ?? 'No especificado')
                            ->icon('heroicon-o-hand-raised'),
                        TextEntry::make('servicioSocial.estatus')
                            ->label('Estatus del Servicio Social')
                            ->badge()
                            ->default(fn () => $this->getServicioSocial()?->estatus ?? 'no_solicitado')
                            ->color(fn (string $state): string => match ($state) {
                                'liberado' => 'success',
                                'en_progreso' => 'info',
                                'pendiente' => 'warning',
                                'no_solicitado' => 'gray',
                                default => 'gray',
                            })
                            ->formatStateUsing(fn (string $state): string => match ($state) {
                                'liberado' => 'Liberado',
                                'en_progreso' => 'En progreso',
                                'pendiente' => 'Pendiente',
                                'no_solicitado' => 'No solicitado',
                                default => $state,
                            })
                            ->icon('heroicon-o-information-circle'),
                    ])
                    ->columns(2),
            ]);
    }

    public function agregarComentario($documentoId, $contenido)
    {
        $documento = Documento::find($documentoId);
        
        if (!$documento) {
            Notification::make()
                ->title('Error')
                ->body('Documento no encontrado.')
                ->danger()
                ->send();
            return;
        }

        if (empty($contenido)) {
            Notification::make()
                ->title('Error')
                ->body('El comentario no puede estar vacío.')
                ->danger()
                ->send();
            return;
        }

        Comentario::create([
            'contenido' => $contenido,
            'tipo' => Auth::user()->role === 'admin' ? 'admin' : 'estudiante',
            'comentable_type' => 'App\\Models\\Documento',
            'comentable_id' => $documento->id,
            'user_id' => Auth::id(),
            'leido' => false
        ]);

        Notification::make()
            ->title('Comentario agregado')
            ->body('Tu comentario ha sido agregado correctamente.')
            ->success()
            ->send();

        $this->dispatch('refresh-table');
        $this->dispatch('close-modal', id: 'nuevo_comentario');
    }

    public function cambiarEstado($documentoId, $nuevoEstatus, $comentario)
    {
        $documento = Documento::find($documentoId);
        
        if (!$documento) {
            Notification::make()
                ->title('Error')
                ->body('Documento no encontrado.')
                ->danger()
                ->send();
            return;
        }

        if (empty($nuevoEstatus)) {
            Notification::make()
                ->title('Error')
                ->body('Debes seleccionar un estado.')
                ->danger()
                ->send();
            return;
        }

        $documento->update([
            'estatus' => $nuevoEstatus,
            'comentario_admin' => $comentario ?? null
        ]);

        if (!empty($comentario)) {
            Comentario::create([
                'contenido' => 'Cambio de estado a "' . $nuevoEstatus . '": ' . $comentario,
                'tipo' => 'admin',
                'comentable_type' => 'App\\Models\\Documento',
                'comentable_id' => $documento->id,
                'user_id' => Auth::id(),
                'leido' => false
            ]);
        }

        Notification::make()
            ->title('Estado actualizado')
            ->body("El documento ha sido cambiado a: " . $nuevoEstatus)
            ->success()
            ->send();

        $this->dispatch('refresh-table');
        $this->dispatch('close-modal', id: 'cambiar_estado');
    }

    public function table(Table $table): Table
    {
        $ordenDocumentos = [
            'Solicitud de Servicio Social',
            'Elección de Modalidad',
            'Carta de Presentación de Servicio Social',
            'Carta de Aceptación',
            'Primer Informe de Actividades Trimestral',
            'Segundo Informe de Actividades Trimestral',
            'Evaluación de Competencias del Desempeño',
            'Carta de Liberación de Servicio Social'
        ];

        return $table
            ->query(
                Documento::query()
                    ->where('user_id', $this->record->id)
                    ->where('activo', true)
                    ->whereHas('tipoDocumento', fn($q) => $q->where('tramite', 'SS'))
                    ->with(['tipoDocumento', 'comentarios.user'])
            )
            
            ->extraAttributes(['class' => 'documents-table'])
            ->columns([
                TextColumn::make('tipoDocumento.nombre')
                    ->label('Documento')
                    ->searchable()
                    ->sortable()
                    ->extraAttributes(['class' => 'doc-col-name']),

                IconColumn::make('archivo_pdf')
                    ->label('Subido')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger')
                    ->state(fn ($record) => !is_null($record->archivo_pdf))
                    ->extraAttributes(['class' => 'doc-col-uploaded']),

                TextColumn::make('estatus')
                    ->label('Estado')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'validado' => 'blue',
                        'validado_ventanilla' => 'success',
                        'rechazado' => 'danger',
                        'pendiente' => 'warning',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'validado' => 'Entregar en Ventanilla',
                        'validado_ventanilla' => 'Validado en Ventanilla',
                        'rechazado' => 'Rechazado',
                        'pendiente' => 'Pendiente',
                        default => $state,
                    })
                    ->extraAttributes(['class' => 'doc-col-status']),
            ])
            ->defaultSort(function ($query) use ($ordenDocumentos) {
                $query->orderByRaw(
                    "FIELD(
                        (SELECT nombre FROM tipos_documento WHERE tipos_documento.id = documentos.tipo_documento_id), 
                        ?,
                        ?,
                        ?,
                        ?,
                        ?,
                        ?,
                        ?,
                        ?
                    )",
                    $ordenDocumentos
                );
            })
            ->actions([
                Action::make('ver_pdf')
                    ->label('Ver PDF')
                    ->icon('heroicon-o-eye')
                    ->color('info')
                    ->extraAttributes(['class' => 'doc-action view-pdf'])
                    ->visible(fn ($record) => !is_null($record->archivo_pdf))
                    ->modalHeading('')
                    ->modalContent(fn ($record) => view('filament.modals.view-pdf-modal', ['record' => $record]))
                    ->modalSubmitAction(false)
                    ->modalCancelActionLabel('Cerrar')
                    ->modalWidth('6xl'),

                Action::make('ver_comentarios')
                    ->label('Ver Comentarios')
                    ->icon('heroicon-o-chat-bubble-left-right')
                    ->color('gray')
                    ->extraAttributes(['class' => 'doc-action view-comments'])
                    ->visible(fn ($record) => $record->comentarios->isNotEmpty())
                    ->modalHeading('')
                    ->modalContent(fn ($record) => view('filament.modals.view-comentarios-modal', ['record' => $record]))
                    ->modalSubmitAction(false)
                    ->modalCancelActionLabel('Cerrar')
                    ->modalWidth('lg'),

                Action::make('nuevo_comentario')
                    ->label('Nuevo Comentario')
                    ->icon('heroicon-o-chat-bubble-left-right')
                    ->color('success')
                    ->extraAttributes(['class' => 'doc-action add-comment'])
                    ->modalHeading('Nuevo Comentario')
                    ->modalDescription('Agrega un comentario a este documento')
                    ->form([
                        Textarea::make('contenido')
                            ->label('Comentario')
                            ->placeholder('Escribe tu comentario aquí...')
                            ->rows(4)
                            ->required()
                            ->maxLength(500)
                            ->helperText('Máximo 500 caracteres'),
                    ])
                    ->action(function (array $data, $record) {
                        $this->agregarComentario($record->id, $data['contenido']);
                    })
                    ->modalSubmitActionLabel('Enviar Comentario')
                    ->modalCancelActionLabel('Cancelar')
                    ->modalWidth('md'),

                Action::make('cambiar_estado')
                    ->label('Cambiar Estado')
                    ->icon('heroicon-o-arrow-path')
                    ->color('warning')
                    ->extraAttributes(['class' => 'doc-action change-status'])
                    ->visible(fn () => Auth::user()->role === 'admin')
                    ->modalHeading('Cambiar Estado del Documento')
                    ->modalDescription('Selecciona el nuevo estado y opcionalmente agrega un comentario')
                    ->form([
                        Select::make('nuevo_estatus')
                            ->label('Nuevo Estado')
                            ->options([
                                'pendiente' => 'Pendiente',
                                'validado' => 'Entregar en Ventanilla',
                                'validado_ventanilla' => 'Validado en Ventanilla',
                                'rechazado' => 'Rechazado',
                            ])
                            ->required()
                            ->placeholder('Selecciona un estado...')
                            ->helperText('Selecciona el estado que tendrá el documento'),
                        Textarea::make('comentario')
                            ->label('Comentario (opcional)')
                            ->placeholder('Agrega un comentario sobre este cambio de estado...')
                            ->rows(3)
                            ->maxLength(500)
                            ->helperText('Máximo 500 caracteres'),
                    ])
                    ->action(function (array $data, $record) {
                        $this->cambiarEstado($record->id, $data['nuevo_estatus'], $data['comentario'] ?? null);
                    })
                    ->modalSubmitActionLabel('Actualizar Estado')
                    ->modalCancelActionLabel('Cancelar')
                    ->modalWidth('md'),
            ])
            ->emptyStateHeading('No hay documentos subidos')
            ->emptyStateDescription('Este estudiante aún no ha subido ningún documento.')
            ->emptyStateIcon('heroicon-o-document-text');
    }

    protected function getStatusColor(string $status): string
    {
        return match ($status) {
            'validado' => 'blue',
            'validado_ventanilla' => 'success',
            'rechazado' => 'red',
            'pendiente' => 'yellow',
            default => 'gray',
        };
    }

    protected function getStatusLabel(string $status): string
    {
        return match ($status) {
            'validado' => 'Entregar en Ventanilla',
            'validado_ventanilla' => 'Validado en Ventanilla',
            'rechazado' => 'Rechazado',
            'pendiente' => 'Pendiente',
            default => $status,
        };
    }

    protected function getHeaderActions(): array
    {
        $servicioSocial = $this->getServicioSocial();

        return [];
    }
}