<?php

namespace App\Filament\Resources\ServicioSocials;

use App\Filament\Resources\ServicioSocials\Pages\CreateServicioSocial;
use App\Filament\Resources\ServicioSocials\Pages\EditServicioSocial;
use App\Filament\Resources\ServicioSocials\Pages\ListServicioSocials;
use App\Filament\Resources\ServicioSocials\Pages\ViewServicioSocial;
use App\Filament\Resources\ServicioSocials\Schemas\ServicioSocialForm;
use App\Filament\Resources\ServicioSocials\Tables\ServicioSocialsTable;
use App\Models\User;
use App\Models\ServicioSocial;
use App\Models\Periodo;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Filament\Tables\Filters\SelectFilter;
use Filament\Actions\Action;
use Filament\Forms\Components\Select as FormSelect;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;

class ServicioSocialResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $recordTitleAttribute = 'name';

    public static function getRecordTitle($record): string
    {
        return $record->name . ' ' . $record->apellidos;
    }

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBriefcase;

    protected static ?string $navigationLabel = 'Servicio Social';

    protected static ?string $pluralModelLabel = 'Servicio Social';

    protected static ?int $navigationSort = 1;

    public static function getNavigationGroup(): ?string
    {
        return '📁 Gestión de Trámites';
    }

    public static function form(Schema $schema): Schema
    {
        return ServicioSocialForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        $table = ServicioSocialsTable::configure($table);

        $table->query(
            User::query()
                ->with('servicioSocial')
                ->with('periodos')
        );

        // ✅ HEADER ACTIONS (FILTRO + CREAR PERIODO)
        $table->headerActions([
            // 📅 FILTRO POR GENERACIÓN (MODAL MEJORADO)
            Action::make('filtrar_periodo')
                ->label('Filtrar por Generación')
                ->icon('heroicon-o-funnel')
                ->color('primary')
                ->extraAttributes(['class' => 'filter-action'])
                ->modalHeading('🔍 Filtrar por Generación')
                ->modalDescription('Selecciona una generación para filtrar los alumnos.')
                ->modalWidth('lg')
                ->form([
                    // 📌 SELECT DE GENERACIÓN (CON COLORES Y CONTADOR)
                    FormSelect::make('periodo_id')
                        ->label('Selecciona una generación')
                        ->native(false)
                        ->allowHtml()
                        ->options(function ($get) {
                            $mostrarTodas = $get('mostrar_todas');
                            $query = Periodo::orderBy('año_inicio', 'desc');
                            
                            if (!$mostrarTodas) {
                                $query->limit(3);
                            }
                            
                            return $query->get()
                                ->mapWithKeys(function ($periodo) {
                                    $color = $periodo->activo ? '#10b981' : '#ef4444';
                                    $status = $periodo->activo ? '● Activo' : '● Inactivo';
                                    $label = "<span style='color: {$color}; font-weight: 600;'>{$status}</span> {$periodo->nombre}";
                                    
                                    $count = User::whereHas('periodos', function ($q) use ($periodo) {
                                        $q->where('periodo_id', $periodo->id);
                                    })->count();
                                    
                                    $label .= " <span style='color: #6b7280; font-size: 0.75rem;'>({$count} alumno" . ($count != 1 ? 's' : '') . ")</span>";
                                    
                                    return [$periodo->id => $label];
                                })
                                ->toArray();
                        })
                        ->placeholder('Selecciona una generación')
                        ->searchable()
                        ->extraAttributes(['class' => 'filter-select-with-colors'])
                        ->helperText('Selecciona la generación que deseas filtrar.'),

                    // 📌 TOGGLE "MOSTRAR TODAS"
                    Toggle::make('mostrar_todas')
                        ->label('Mostrar todas las generaciones')
                        ->default(false)
                        ->live()
                        ->afterStateUpdated(function ($state, $set, $get) {
                            $set('periodo_id', null);
                        })
                        ->helperText('Activa esta opción para ver todas las generaciones disponibles.')
                        ->extraAttributes(['class' => 'filter-toggle']),
                ])
                ->action(function (array $data) {
                    session(['periodo_id_servicio_social' => $data['periodo_id']]);
                    session(['mostrar_todas_generaciones' => $data['mostrar_todas'] ?? false]);
                    
                    if ($data['periodo_id']) {
                        $periodo = Periodo::find($data['periodo_id']);
                        Notification::make()
                            ->title('✅ Filtro aplicado')
                            ->body("Mostrando alumnos de la generación: {$periodo->nombre}")
                            ->success()
                            ->send();
                    } else {
                        Notification::make()
                            ->title('🔍 Filtro eliminado')
                            ->body('Mostrando TODOS los alumnos')
                            ->info()
                            ->send();
                    }
                    
                    return redirect()->route('filament.admin.resources.servicio-socials.index', [
                        'periodo_id' => $data['periodo_id']
                    ]);
                })
                ->modalSubmitActionLabel('Aplicar filtro')
                ->modalCancelActionLabel('Cancelar')
                ->modalWidth('lg')
                ->extraModalFooterActions([
                    Action::make('reset_filtro')
                        ->label('Resetear filtro')
                        ->icon('heroicon-o-arrow-path')
                        ->color('danger')
                        ->extraAttributes(['class' => 'reset-filter-btn'])
                        ->action(function () {
                            session()->forget('periodo_id_servicio_social');
                            session()->forget('mostrar_todas_generaciones');
                            Notification::make()
                                ->title('🔄 Filtro reseteado')
                                ->body('Mostrando TODOS los alumnos')
                                ->info()
                                ->send();
                            return redirect()->route('filament.admin.resources.servicio-socials.index');
                        }),
                ]),

            // ✅ BOTÓN: CREAR GENERACIÓN
            Action::make('crear_periodo')
                ->label('Crear generación')
                ->icon('heroicon-o-plus-circle')
                ->color('success')
                ->extraAttributes(['class' => 'create-period-btn'])
                ->url('/admin/periodos/create')
                ->openUrlInNewTab(false),
        ]);

        // ✅ ELIMINAR FILTROS DE LA TABLA
        $table->filters([]);

        // ✅ INDICADOR DE FILTRO ACTIVO
        $periodoId = session('periodo_id_servicio_social', request()->get('periodo_id'));
        
        if ($periodoId) {
            $periodo = Periodo::find($periodoId);
            if ($periodo) {
                $table->heading("📌 Mostrando alumnos de la generación: {$periodo->nombre}");
                
                $table->modifyQueryUsing(function ($query) use ($periodoId) {
                    $query->whereHas('periodos', function ($sq) use ($periodoId) {
                        $sq->where('periodo_id', $periodoId);
                    });
                });
            }
        } else {
            $table->heading("📋 Mostrando TODOS los alumnos");
        }

        return $table;
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListServicioSocials::route('/'),
            // 'create' => CreateServicioSocial::route('/create'),
            'edit' => EditServicioSocial::route('/{record}/edit'),
            'view' => ViewServicioSocial::route('/{record}'),
        ];
    }
}