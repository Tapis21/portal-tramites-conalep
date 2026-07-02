<?php

namespace App\Filament\Resources\ServicioSocials\Pages;

use App\Filament\Resources\ServicioSocials\ServicioSocialResource;
use App\Models\Documento;
use App\Models\Comentario;
use App\Models\ServicioSocial;
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

    // Variables para los modales
    public $comentario_contenido = '';
    public $nuevo_estatus = '';
    public $comentario_estado = '';
    public $nuevo_estatus_estudiante = '';
    public $comentario_estatus_estudiante = '';

    public function infolist(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Section::make('👤 Datos del Estudiante')
                    ->schema([
                        TextEntry::make('user.name')
                            ->label('Nombre completo')
                            ->icon('heroicon-o-user'),
                        TextEntry::make('user.matricula')
                            ->label('Matrícula')
                            ->icon('heroicon-o-identification'),
                        TextEntry::make('user.carrera')
                            ->label('Carrera')
                            ->icon('heroicon-o-academic-cap'),
                        TextEntry::make('user.semestre')
                            ->label('Semestre')
                            ->formatStateUsing(fn ($state) => $state . '° Semestre')
                            ->icon('heroicon-o-numbered-list'),
                        TextEntry::make('user.grupo')
                            ->label('Grupo')
                            ->placeholder('No asignado')
                            ->icon('heroicon-o-users'),
                        TextEntry::make('user.nombre_turno')
                            ->label('Turno')
                            ->icon('heroicon-o-clock'),
                    ])
                    ->columns(3),

                Section::make('🏢 Datos de la Empresa')
                    ->schema([
                        TextEntry::make('empresa.nombre')
                            ->label('Empresa')
                            ->icon('heroicon-o-building-office'),
                        TextEntry::make('area_asignada')
                            ->label('Área asignada')
                            ->placeholder('No especificada')
                            ->icon('heroicon-o-map-pin'),
                    ])
                    ->columns(2),

                Section::make('📅 Fechas y Horas')
                    ->schema([
                        TextEntry::make('fecha_inicio')
                            ->label('Fecha de inicio')
                            ->date('d/m/Y')
                            ->icon('heroicon-o-calendar'),
                        TextEntry::make('fecha_limite_segundo_informe')
                            ->label('Fecha de finalización')
                            ->date('d/m/Y')
                            ->icon('heroicon-o-calendar-days'),
                        TextEntry::make('horario.hora_inicio')
                            ->label('Horario')
                            ->formatStateUsing(fn ($record) => 
                                $record->horario ? $record->horario->hora_inicio . ' - ' . $record->horario->hora_fin : 'No definido'
                            )
                            ->icon('heroicon-o-clock'),
                        TextEntry::make('fecha_limite_primer_informe')
                            ->label('Límite primer informe')
                            ->date('d/m/Y')
                            ->icon('heroicon-o-calendar'),
                        TextEntry::make('fecha_limite_segundo_informe')
                            ->label('Límite segundo informe')
                            ->date('d/m/Y')
                            ->icon('heroicon-o-calendar'),
                    ])
                    ->columns(3),

                Section::make('📋 Datos de Contacto')
                    ->schema([
                        TextEntry::make('gradoAcademico.abreviatura')
                            ->label('Grado (Carta)')
                            ->icon('heroicon-o-user-circle'),
                        TextEntry::make('nombre_persona_carta')
                            ->label('Nombre de la persona')
                            ->icon('heroicon-o-user'),
                        TextEntry::make('cargo_persona_carta')
                            ->label('Cargo de la persona')
                            ->icon('heroicon-o-briefcase'),
                        TextEntry::make('gradoAcademicoJefe.abreviatura')
                            ->label('Grado (Jefe)')
                            ->icon('heroicon-o-user-circle'),
                        TextEntry::make('nombre_jefe_inmediato')
                            ->label('Nombre del jefe inmediato')
                            ->icon('heroicon-o-user'),
                        TextEntry::make('cargo_jefe_inmediato')
                            ->label('Cargo del jefe inmediato')
                            ->icon('heroicon-o-briefcase'),
                    ])
                    ->columns(3),

                Section::make('ℹ️ Información Adicional')
                    ->schema([
                        TextEntry::make('apoyo_estudiante')
                            ->label('Apoyo al estudiante')
                            ->placeholder('No especificado')
                            ->icon('heroicon-o-hand-raised'),
                        TextEntry::make('estatus')
                            ->label('Estatus del Servicio Social')
                            ->badge()
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
                            ->icon('heroicon-o-information-circle')
                            ->extraAttributes(function ($record) {
                                if (Auth::user()->role === 'admin') {
                                    return [
                                        'class' => 'cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors rounded-lg p-2',
                                        'wire:click' => '$dispatch("open-modal", { id: "cambiar-estatus-estudiante" })',
                                    ];
                                }
                                return [];
                            }),
                    ])
                    ->columns(2),
            ]);
    }

    // 🔥 Acción para actualizar estatus del estudiante
    public function actualizarEstatusEstudiante()
    {
        $servicioSocial = ServicioSocial::where('user_id', $this->record->user_id)->first();
        
        if (!$servicioSocial) {
            Notification::make()
                ->title('Error')
                ->body('No se encontró el registro de Servicio Social.')
                ->danger()
                ->send();
            return;
        }

        if (empty($this->nuevo_estatus_estudiante)) {
            Notification::make()
                ->title('Error')
                ->body('Debes seleccionar un estatus.')
                ->danger()
                ->send();
            return;
        }

        $estatusAnterior = $servicioSocial->estatus;
        
        $servicioSocial->update([
            'estatus' => $this->nuevo_estatus_estudiante
        ]);

        // Actualizar también el campo estatus_servicio_social en users
        $servicioSocial->user->update([
            'estatus_servicio_social' => $this->nuevo_estatus_estudiante
        ]);

        if (!empty($this->comentario_estatus_estudiante)) {
            Comentario::create([
                'contenido' => 'Cambio de estatus de "' . $estatusAnterior . '" a "' . $this->nuevo_estatus_estudiante . '": ' . $this->comentario_estatus_estudiante,
                'tipo' => 'admin',
                'comentable_type' => 'App\\Models\\ServicioSocial',
                'comentable_id' => $servicioSocial->id,
                'user_id' => Auth::id(),
                'leido' => false
            ]);
        }

        $this->nuevo_estatus_estudiante = '';
        $this->comentario_estatus_estudiante = '';

        Notification::make()
            ->title('Estatus actualizado')
            ->body("El estatus del estudiante ha sido cambiado a: " . $this->nuevo_estatus_estudiante)
            ->success()
            ->send();

        $this->dispatch('refresh-table');
    }

    // 🔥 Acción para agregar comentario
    public function agregarComentario($documentoId)
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

        if (empty($this->comentario_contenido)) {
            Notification::make()
                ->title('Error')
                ->body('El comentario no puede estar vacío.')
                ->danger()
                ->send();
            return;
        }

        Comentario::create([
            'contenido' => $this->comentario_contenido,
            'tipo' => Auth::user()->role === 'admin' ? 'admin' : 'estudiante',
            'comentable_type' => 'App\\Models\\Documento',
            'comentable_id' => $documento->id,
            'user_id' => Auth::id(),
            'leido' => false
        ]);

        $this->comentario_contenido = '';

        Notification::make()
            ->title('Comentario agregado')
            ->body('Tu comentario ha sido agregado correctamente.')
            ->success()
            ->send();

        $this->dispatch('refresh-table');
    }

    // 🔥 Acción para cambiar estado del documento
    public function cambiarEstado($documentoId)
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

        if (empty($this->nuevo_estatus)) {
            Notification::make()
                ->title('Error')
                ->body('Debes seleccionar un estado.')
                ->danger()
                ->send();
            return;
        }

        $documento->update([
            'estatus' => $this->nuevo_estatus,
            'comentario_admin' => $this->comentario_estado ?? null
        ]);

        if (!empty($this->comentario_estado)) {
            Comentario::create([
                'contenido' => 'Cambio de estado a "' . $this->nuevo_estatus . '": ' . $this->comentario_estado,
                'tipo' => 'admin',
                'comentable_type' => 'App\\Models\\Documento',
                'comentable_id' => $documento->id,
                'user_id' => Auth::id(),
                'leido' => false
            ]);
        }

        $this->nuevo_estatus = '';
        $this->comentario_estado = '';

        Notification::make()
            ->title('Estado actualizado')
            ->body("El documento ha sido cambiado a: " . $this->nuevo_estatus)
            ->success()
            ->send();

        $this->dispatch('refresh-table');
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
                    ->where('user_id', $this->record->user_id)
                    ->where('activo', true)
                    ->whereHas('tipoDocumento', fn($q) => $q->where('tramite', 'SS'))
                    ->with(['tipoDocumento', 'comentarios.user'])
            )
            ->columns([
                TextColumn::make('tipoDocumento.nombre')
                    ->label('Documento')
                    ->searchable()
                    ->sortable(),

                IconColumn::make('archivo_pdf')
                    ->label('Subido')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger')
                    ->state(fn ($record) => !is_null($record->archivo_pdf)),

                TextColumn::make('estatus')
                    ->label('Estado')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'validado' => 'success',
                        'validado_ventanilla' => 'info',
                        'rechazado' => 'danger',
                        'pendiente' => 'warning',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'validado' => '✅ Validado',
                        'validado_ventanilla' => '📄 Validado en Ventanilla',
                        'rechazado' => '❌ Rechazado',
                        'pendiente' => '⏳ Pendiente',
                        default => $state,
                    }),
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
                // 👁️ Ver PDF
                Action::make('ver_pdf')
                    ->label('Ver PDF')
                    ->icon('heroicon-o-eye')
                    ->color('info')
                    ->visible(fn ($record) => !is_null($record->archivo_pdf))
                    ->modalHeading('')
                    ->modalContent(fn ($record) => view('filament.modals.view-pdf-modal', ['record' => $record]))
                    ->modalSubmitAction(false)
                    ->modalCancelActionLabel('Cerrar')
                    ->modalWidth('6xl'),

                // 💬 Ver comentarios
                Action::make('ver_comentarios')
                    ->label('Ver Comentarios')
                    ->icon('heroicon-o-chat-bubble-left-right')
                    ->color('gray')
                    ->visible(fn ($record) => $record->comentarios->isNotEmpty())
                    ->modalHeading('')
                    ->modalContent(fn ($record) => view('filament.modals.view-comentarios-modal', ['record' => $record]))
                    ->modalSubmitAction(false)
                    ->modalCancelActionLabel('Cerrar')
                    ->modalWidth('lg'),

                // ✏️ Nuevo comentario
                Action::make('nuevo_comentario')
                    ->label('Nuevo Comentario')
                    ->icon('heroicon-o-plus-circle')
                    ->color('success')
                    ->modalHeading('Nuevo Comentario')
                    ->modalContent(fn ($record) => view('filament.modals.nuevo-comentario-modal', [
                        'record' => $record,
                        'documentoId' => $record->id
                    ]))
                    ->modalActions([
                        Action::make('cerrar')
                            ->label('Cerrar')
                            ->color('gray')
                            ->action(fn () => $this->dispatch('close-modal', 'nuevo_comentario')),
                    ])
                    ->modalWidth('md'),

                // ✏️ Cambiar estado del documento
                Action::make('cambiar_estado')
                    ->label('Cambiar Estado')
                    ->icon('heroicon-o-pencil-square')
                    ->color('warning')
                    ->visible(fn () => Auth::user()->role === 'admin')
                    ->modalHeading('Cambiar Estado del Documento')
                    ->modalContent(fn ($record) => view('filament.modals.cambiar-estado-modal', [
                        'record' => $record,
                        'documentoId' => $record->id
                    ]))
                    ->modalActions([
                        Action::make('cerrar')
                            ->label('Cerrar')
                            ->color('gray')
                            ->action(fn () => $this->dispatch('close-modal', 'cambiar_estado')),
                    ])
                    ->modalWidth('md'),
            ])
            ->emptyStateHeading('No hay documentos subidos')
            ->emptyStateDescription('Este estudiante aún no ha subido ningún documento.')
            ->emptyStateIcon('heroicon-o-document-text');
    }

    protected function getStatusColor(string $status): string
    {
        return match ($status) {
            'validado' => 'green',
            'validado_ventanilla' => 'blue',
            'rechazado' => 'red',
            'pendiente' => 'yellow',
            default => 'gray',
        };
    }

    protected function getStatusLabel(string $status): string
    {
        return match ($status) {
            'validado' => '✅ Validado',
            'validado_ventanilla' => '📄 Validado en Ventanilla',
            'rechazado' => '❌ Rechazado',
            'pendiente' => '⏳ Pendiente',
            default => $status,
        };
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make()->label('Editar')->icon('heroicon-o-pencil-square')->color('primary'),
        ];
    }
}