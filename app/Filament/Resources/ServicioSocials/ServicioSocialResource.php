<?php

namespace App\Filament\Resources\ServicioSocials;

use App\Filament\Resources\ServicioSocials\Pages\CreateServicioSocial;
use App\Filament\Resources\ServicioSocials\Pages\EditServicioSocial;
use App\Filament\Resources\ServicioSocials\Pages\ListServicioSocials;
use App\Filament\Resources\ServicioSocials\Pages\ViewServicioSocial;
use App\Filament\Resources\ServicioSocials\Schemas\ServicioSocialForm;
use App\Filament\Resources\ServicioSocials\Tables\ServicioSocialsTable;
use App\Models\User;  // ✅ USAR User COMO MODELO BASE
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
use Filament\Notifications\Notification;

class ServicioSocialResource extends Resource
{
    // ✅ CAMBIAR A User COMO MODELO BASE
    protected static ?string $model = User::class;

    protected static ?string $recordTitleAttribute = 'id';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBriefcase;

    protected static ?string $navigationLabel = 'Servicio Social';

    protected static ?string $pluralModelLabel = 'Servicio Social';

    protected static ?int $navigationSort = 1;
    public static function getNavigationGroup(): ?string
    {
        return '📁 Gestión de Trámites';
    }

    // public static function getNavigationGroup(): ?string
    // {
    //     return 'Servicio Social';
    // }

    public static function form(Schema $schema): Schema
    {
        return ServicioSocialForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        $table = ServicioSocialsTable::configure($table);

        // ✅ QUERY BASE: TODOS LOS USUARIOS CON SU RELACIÓN A SERVICIO SOCIAL
        $table->query(
            User::query()
                ->with('servicioSocial')
                ->with('periodos')
        );

        // ✅ FILTRO SUPERIOR POR PERIODO
        $table->headerActions([
            Action::make('filtrar_periodo')
                ->label('📅 Filtrar por Periodo')
                ->icon('heroicon-o-funnel')
                ->color('primary')
                ->form([
                    FormSelect::make('periodo_id')
                        ->label('Selecciona un periodo para filtrar los alumnos')
                        ->options(
                            Periodo::orderBy('año_inicio', 'desc')
                                ->get()
                                ->mapWithKeys(function ($periodo) {
                                    $label = $periodo->nombre;
                                    if ($periodo->activo) {
                                        $label .= ' ✅ (Activo)';
                                    } else {
                                        $label .= ' ❌ (Inactivo)';
                                    }
                                    $count = User::whereHas('periodos', function ($q) use ($periodo) {
                                        $q->where('periodo_id', $periodo->id);
                                    })->count();
                                    $label .= " ($count alumnos)";
                                    return [$periodo->id => $label];
                                })
                                ->toArray()
                        )
                        ->placeholder('🔍 Mostrar todos los periodos')
                        ->default(request()->get('periodo_id') ?? session('periodo_id_servicio_social', null))
                        ->searchable()
                        ->native(false),
                ])
                ->action(function (array $data) {
                    session(['periodo_id_servicio_social' => $data['periodo_id']]);
                    
                    if ($data['periodo_id']) {
                        $periodo = Periodo::find($data['periodo_id']);
                        Notification::make()
                            ->title('✅ Filtro aplicado')
                            ->body("Mostrando alumnos del periodo: {$periodo->nombre}")
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
                ->modalWidth('md')
                ->extraModalFooterActions([
                    Action::make('reset_filtro')
                        ->label('🔄 Resetear filtro')
                        ->color('danger')
                        ->action(function () {
                            session()->forget('periodo_id_servicio_social');
                            Notification::make()
                                ->title('🔄 Filtro reseteado')
                                ->body('Mostrando TODOS los alumnos')
                                ->info()
                                ->send();
                            return redirect()->route('filament.admin.resources.servicio-socials.index');
                        }),
                ]),
        ]);

        // ✅ ELIMINAR FILTROS DE LA TABLA
        $table->filters([]);

        // ✅ INDICADOR DE FILTRO ACTIVO
        $periodoId = session('periodo_id_servicio_social', request()->get('periodo_id'));
        
        if ($periodoId) {
            $periodo = Periodo::find($periodoId);
            if ($periodo) {
                $table->heading("📌 Mostrando alumnos del periodo: {$periodo->nombre}");
                
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
            'create' => CreateServicioSocial::route('/create'),
            'edit' => EditServicioSocial::route('/{record}/edit'),
            'view' => ViewServicioSocial::route('/{record}'),
        ];
    }
}