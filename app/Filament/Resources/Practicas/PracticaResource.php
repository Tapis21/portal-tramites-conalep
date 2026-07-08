<?php

namespace App\Filament\Resources\Practicas;

use App\Filament\Resources\Practicas\Pages\CreatePractica;
use App\Filament\Resources\Practicas\Pages\EditPractica;
use App\Filament\Resources\Practicas\Pages\ListPracticas;
use App\Filament\Resources\Practicas\Pages\ViewPractica;
use App\Filament\Resources\Practicas\Schemas\PracticaForm;
use App\Filament\Resources\Practicas\Tables\PracticasTable;
use App\Models\Practica;
use App\Models\Periodo;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Filament\Tables\Filters\SelectFilter;

// ✅ AGREGAR ESTOS USE
use Filament\Actions\Action;
use Filament\Forms\Components\Select as FormSelect;
use Filament\Notifications\Notification;

class PracticaResource extends Resource
{
    protected static ?string $model = Practica::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedAcademicCap;

    protected static ?string $navigationLabel = 'Prácticas Profesionales';

    protected static ?string $pluralModelLabel = 'Prácticas Profesionales';

    public static function getNavigationGroup(): ?string
    {
        return 'Prácticas Profesionales';
    }

    public static function form(Schema $schema): Schema
    {
        return PracticaForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        $table = PracticasTable::configure($table);

        // ✅ FILTRO SUPERIOR (EL ÚNICO QUE SE USA)
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
                                    $count = \App\Models\User::whereHas('periodos', function ($q) use ($periodo) {
                                        $q->where('periodo_id', $periodo->id);
                                    })->count();
                                    $label .= " ($count alumnos)";
                                    return [$periodo->id => $label];
                                })
                                ->toArray()
                        )
                        ->placeholder('🔍 Mostrar todos los periodos')
                        ->default(request()->get('periodo_id') ?? session('periodo_id_practicas', null))
                        ->searchable()
                        ->native(false),
                ])
                ->action(function (array $data) {
                    session(['periodo_id_practicas' => $data['periodo_id']]);
                    
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
                            ->body('Mostrando TODOS los alumnos, sin filtrar por periodo')
                            ->info()
                            ->send();
                    }
                    
                    return redirect()->route('filament.admin.resources.practicas.index', [
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
                            session()->forget('periodo_id_practicas');
                            Notification::make()
                                ->title('🔄 Filtro reseteado')
                                ->body('Mostrando TODOS los alumnos del sistema')
                                ->info()
                                ->send();
                            return redirect()->route('filament.admin.resources.practicas.index');
                        }),
                ]),
        ]);

        // ✅ ELIMINAR FILTROS DE LA TABLA (sidebar)
        $table->filters([]);

        // ✅ INDICADOR DE FILTRO ACTIVO
        $periodoId = session('periodo_id_practicas', request()->get('periodo_id'));
        
        if ($periodoId) {
            $periodo = Periodo::find($periodoId);
            if ($periodo) {
                $table->heading("📌 Mostrando alumnos del periodo: {$periodo->nombre}");
                
                $table->modifyQueryUsing(function ($query) use ($periodoId) {
                    $query->whereHas('user', function ($q) use ($periodoId) {
                        $q->whereHas('periodos', function ($sq) use ($periodoId) {
                            $sq->where('periodo_id', $periodoId);
                        });
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
            'index' => ListPracticas::route('/'),
            'create' => CreatePractica::route('/create'),
            'edit' => EditPractica::route('/{record}/edit'),
            'view' => ViewPractica::route('/{record}'),
        ];
    }
}