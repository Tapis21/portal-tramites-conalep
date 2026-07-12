<?php

namespace App\Filament\Resources\Practicas;

use App\Filament\Resources\Practicas\Pages\CreatePractica;
use App\Filament\Resources\Practicas\Pages\EditPractica;
use App\Filament\Resources\Practicas\Pages\ListPracticas;
use App\Filament\Resources\Practicas\Pages\ViewPractica;
use App\Filament\Resources\Practicas\Schemas\PracticaForm;
use App\Filament\Resources\Practicas\Tables\PracticasTable;
use App\Models\User;
use App\Models\Practica;
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

class PracticaResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $recordTitleAttribute = 'name';

    public static function getRecordTitle($record): string
    {
        return $record->name . ' ' . $record->apellidos;
    }

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedAcademicCap;

    protected static ?string $navigationLabel = 'Prácticas Profesionales';

    protected static ?string $pluralModelLabel = 'Prácticas Profesionales';

    protected static ?int $navigationSort = 2;

    public static function getNavigationGroup(): ?string
    {
        return '📁 Gestión de Trámites';
    }

    public static function form(Schema $schema): Schema
    {
        return PracticaForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        $table = PracticasTable::configure($table);

        $table->query(
            User::query()
                ->with('practicas')
                ->with('periodos')
        );

        // ✅ HEADER ACTIONS (FILTRO + CREAR PERIODO)
        $table->headerActions([
            Action::make('filtrar_periodo')
                ->label('Filtrar por Periodo')
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
                        ->placeholder('Mostrar todos los periodos')
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
                            ->body('Mostrando TODOS los alumnos')
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
                        ->label('Resetear filtro')
                        ->icon('heroicon-o-arrow-path')
                        ->color('danger')
                        ->action(function () {
                            session()->forget('periodo_id_practicas');
                            Notification::make()
                                ->title('🔄 Filtro reseteado')
                                ->body('Mostrando TODOS los alumnos')
                                ->info()
                                ->send();
                            return redirect()->route('filament.admin.resources.practicas.index');
                        }),
                ]),

            Action::make('crear_periodo')
                ->label('Crear periodo')
                ->icon('heroicon-o-plus-circle')
                ->color('success')
                ->url('/admin/periodos/create')
                ->openUrlInNewTab(false),
        ]);

        $table->filters([]);

        $periodoId = session('periodo_id_practicas', request()->get('periodo_id'));
        
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
            'index' => ListPracticas::route('/'),
            // 'create' => CreatePractica::route('/create'),
            'edit' => EditPractica::route('/{record}/edit'),
            'view' => ViewPractica::route('/{record}'),
        ];
    }
}