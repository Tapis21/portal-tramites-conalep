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

                // ✅ SECCIÓN INFORMACIÓN ADICIONAL CON ACTION NATIVO
                Section::make('ℹ️ Información Adicional')
                    ->footerActions([
                        Actions\Action::make('cambiar_estatus_estudiante')
                            ->label('Cambiar Estatus')
                            ->icon('heroicon-o-pencil-square')
                            ->color('primary')
                            ->visible(fn () => Auth::user()->role === 'admin')
                            ->form([
                                Select::make('nuevo_estatus_estudiante')
                                    ->label('Nuevo Estatus')
                                    ->options([
                                        'no_solicitado' => '⬜ No solicitado',
                                        'pendiente' => '⏳ Pendiente',
                                        'en_progreso' => '🔄 En progreso',
                                        'liberado' => '✅ Liberado',
                                    ])
                                    ->default(fn ($record) => $record->estatus ?? 'no_solicitado')
                                    ->required(),
                                Textarea::make('comentario_estatus_estudiante')
                                    ->label('Comentario (opcional)')
                                    ->placeholder('Agrega un comentario sobre este cambio de estatus...')
                                    ->rows(3)
                                    ->maxLength(500),
                            ])
                            ->action(function (array $data) {
                                $servicioSocial = ServicioSocial::where('user_id', $this->record->user_id)->first();
                                
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

                                // Actualizar también el campo estatus_servicio_social en users
                                $servicioSocial->user->update([
                                    'estatus_servicio_social' => $data['nuevo_estatus_estudiante']
                                ]);

                                if (!empty($data['comentario_estatus_estudiante'])) {
                                    Comentario::create([
                                        'contenido' => 'Cambio de estatus de "' . $estatusAnterior . '" a "' . $data['nuevo_estatus_estudiante'] . '": ' . $data['comentario_estatus_estudiante'],
                                        'tipo' => 'admin',
                                        'comentable_type' => 'App\\Models\\ServicioSocial',
                                        'comentable_id' => $servicioSocial->id,
                                        'user_id' => Auth::id(),
                                        'leido' => false
                                    ]);
                                }

                                Notification::make()
                                    ->title('✅ Estatus actualizado')
                                    ->body("El estatus del estudiante ha sido cambiado correctamente.")
                                    ->success()
                                    ->send();

                                $this->dispatch('refresh-table');
                            })
                            ->modalSubmitActionLabel('✅ Actualizar Estatus')
                            ->modalCancelActionLabel('❌ Cancelar')
                            ->modalWidth('md'),
                    ])
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
                            ->icon('heroicon-o-information-circle'),
                    ])
                    ->columns(2),
            ]);
    }

    // 🔥 Acción para agregar comentario (CON FORMULARIO NATIVO + DISEÑO MEJORADO)
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
            ->title('✅ Comentario agregado')
            ->body('Tu comentario ha sido agregado correctamente.')
            ->success()
            ->send();

        $this->dispatch('refresh-table');
        $this->dispatch('close-modal', id: 'nuevo_comentario');
    }

    // 🔥 Acción para cambiar estado del documento (CON FORMULARIO NATIVO + DISEÑO MEJORADO)
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
            ->title('✅ Estado actualizado')
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
                    ->icon('heroicon-o-chat-bubble-left-right')
                    ->color('success')
                    ->modalHeading('💬 Nuevo Comentario')
                    ->modalDescription('📝 Agrega un comentario a este documento')
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
                    ->modalSubmitActionLabel('✨ Enviar Comentario')
                    ->modalCancelActionLabel('❌ Cancelar')
                    ->modalWidth('md'),

                // ✏️ Cambiar estado del documento
                Action::make('cambiar_estado')
                    ->label('Cambiar Estado')
                    ->icon('heroicon-o-arrow-path')
                    ->color('warning')
                    ->visible(fn () => Auth::user()->role === 'admin')
                    ->modalHeading('🔄 Cambiar Estado del Documento')
                    ->modalDescription('📌 Selecciona el nuevo estado y opcionalmente agrega un comentario')
                    ->form([
                        Select::make('nuevo_estatus')
                            ->label('Nuevo Estado')
                            ->options([
                                'pendiente' => '⏳ Pendiente',
                                'validado' => '✅ Validado',
                                'validado_ventanilla' => '📄 Validado en Ventanilla',
                                'rechazado' => '❌ Rechazado',
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
                    ->modalSubmitActionLabel('✅ Actualizar Estado')
                    ->modalCancelActionLabel('❌ Cancelar')
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