<?php

namespace App\Filament\Resources\Configuracions\Pages;

use App\Filament\Resources\Configuracions\ConfiguracionResource;
use Filament\Resources\Pages\Page;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Notifications\Notification;
use Filament\Actions\Action;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\UsersImport;
use App\Imports\EmpresasImport;
use Illuminate\Support\Facades\Storage;

class ImportarDatos extends Page implements HasForms
{
    use InteractsWithForms;

    protected static string $resource = ConfiguracionResource::class;

    protected string $view = 'filament.resources.configuracions.pages.importar-datos';

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill([
            'tipo_importacion' => 'alumnos',
        ]);
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->schema([

                // ================================================================
                // 📊 TARJETAS DE INFORMACIÓN RÁPIDA
                // ================================================================
                Section::make()
                    ->schema([
                        \Filament\Schemas\Components\View::make('filament.components.html-content')
                            ->viewData([
                                'content' => <<<HTML
                                <div class="stats-grid">
                                    <div class="stat-card">
                                        <div class="icon-wrapper blue">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                        </div>
                                        <div>
                                            <div class="stat-title blue">Importación rápida</div>
                                            <div class="stat-desc">Cientos de registros en menos de 2 minutos</div>
                                            <div class="stat-highlight blue">⏱️ Ahorra horas de trabajo manual</div>
                                        </div>
                                    </div>
                                    <div class="stat-card">
                                        <div class="icon-wrapper green">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                            </svg>
                                        </div>
                                        <div>
                                            <div class="stat-title green">Actualización automática</div>
                                            <div class="stat-desc">Sin duplicados, se actualiza si ya existe</div>
                                            <div class="stat-highlight green">🔄 Datos siempre actualizados</div>
                                        </div>
                                    </div>
                                    <div class="stat-card">
                                        <div class="icon-wrapper purple">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                                            </svg>
                                        </div>
                                        <div>
                                            <div class="stat-title purple">Validación de datos</div>
                                            <div class="stat-desc">Manejo de errores claro y controlado</div>
                                            <div class="stat-highlight purple">🛡️ Importación segura</div>
                                        </div>
                                    </div>
                                </div>
                                HTML,
                            ])
                    ])
                    ->collapsible(false)
                    ->compact(),

                // ================================================================
                // 📋 INSTRUCCIONES
                // ================================================================
                Section::make('Instrucciones paso a paso')
                    ->description('Sigue estos 4 pasos simples para importar tus datos.')
                    ->icon('heroicon-o-list-bullet')
                    ->schema([
                        \Filament\Schemas\Components\View::make('filament.components.html-content')
                            ->viewData([
                                'content' => <<<HTML
                                <div class="instructions-grid">
                                    <div class="instruction-step" style="background: #eff6ff; border-color: #bfdbfe;">
                                        <div class="step-number blue">1</div>
                                        <div>
                                            <div class="step-title blue">Elige qué vas a importar</div>
                                            <div class="step-desc">Selecciona si importarás <strong>Alumnos</strong> o <strong>Empresas</strong>.</div>
                                        </div>
                                    </div>
                                    <div class="instruction-step" style="background: #ecfdf5; border-color: #bbf7d0;">
                                        <div class="step-number green">2</div>
                                        <div>
                                            <div class="step-title green">Descarga la plantilla</div>
                                            <div class="step-desc">Obtén el archivo con el formato correcto.</div>
                                        </div>
                                    </div>
                                    <div class="instruction-step" style="background: #fffbeb; border-color: #fde68a;">
                                        <div class="step-number yellow">3</div>
                                        <div>
                                            <div class="step-title yellow">Llena la plantilla</div>
                                            <div class="step-desc">Agrega tus datos. Cada fila es un registro.</div>
                                        </div>
                                    </div>
                                    <div class="instruction-step" style="background: #faf5ff; border-color: #e9d5ff;">
                                        <div class="step-number purple">4</div>
                                        <div>
                                            <div class="step-title purple">Sube y listo</div>
                                            <div class="step-desc">Arrastra tu archivo y haz clic en <strong>Importar</strong>.</div>
                                        </div>
                                    </div>
                                </div>
                                HTML,
                            ])
                    ])
                    ->collapsible(true)
                    ->collapsed(false)
                    ->compact(),

                // ================================================================
                // ℹ️ INFORMACIÓN IMPORTANTE
                // ================================================================
                Section::make('Información importante')
                    ->description('Antes de empezar, revisa qué datos necesitas para cada tipo de importación.')
                    ->icon('heroicon-o-information-circle')
                    ->schema([
                        \Filament\Schemas\Components\View::make('filament.components.html-content')
                            ->viewData([
                                'content' => <<<HTML
                                <div class="tech-grid">
                                    <div class="tech-card blue">
                                        <div class="tech-header">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                                            </svg>
                                            <span class="tech-title">Para Alumnos</span>
                                        </div>
                                        <div>
                                            <div class="tech-subtitle">Datos obligatorios</div>
                                            <ul>
                                                <li>• <strong>Matrícula</strong> (única)</li>
                                                <li>• <strong>Nombre(s)</strong></li>
                                                <li>• <strong>Primer apellido</strong></li>
                                                <li>• <strong>Segundo apellido</strong> (opcional)</li>
                                                <li>• <strong>Grupo</strong> <span style="color:#6b7280;font-size:0.65rem;">(ej: 601-ADMO23)</span></li>
                                                <li>• <strong>Periodo</strong> <span style="color:#6b7280;font-size:0.65rem;">(ej: 2023-2026)</span></li>
                                            </ul>
                                            <div class="tech-tip">
                                                <span>💡</span> El sistema genera el email y contraseña automáticamente.
                                            </div>
                                        </div>
                                    </div>
                                    <div class="tech-card green">
                                        <div class="tech-header">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                            </svg>
                                            <span class="tech-title">Para Empresas</span>
                                        </div>
                                        <div>
                                            <div class="tech-subtitle">Datos obligatorios</div>
                                            <ul>
                                                <li>• <strong>Nombre</strong></li>
                                                <li>• <strong>Dirección</strong> (opcional)</li>
                                                <li>• <strong>Teléfono</strong> (opcional)</li>
                                                <li>• <strong>Contacto</strong> (opcional)</li>
                                                <li>• <strong>Servicio Social</strong> (SI/NO)</li>
                                                <li>• <strong>Prácticas</strong> (SI/NO)</li>
                                                <li>• <strong>Dual</strong> (SI/NO)</li>
                                            </ul>
                                            <div class="tech-tip">
                                                <span>💡</span> El campo "activo" se actualiza automáticamente según la fecha.
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                HTML,
                            ])
                    ])
                    ->collapsible(true)
                    ->collapsed(false)
                    ->compact(),

                // ================================================================
                // 🎯 ¿QUÉ VAS A IMPORTAR?
                // ================================================================
                Section::make('¿Qué vas a importar?')
                    ->description('Elige si importarás alumnos o empresas.')
                    ->icon('heroicon-o-users')
                    ->extraAttributes(['class' => 'section-tipo'])
                    ->schema([
                        Select::make('tipo_importacion')
                            ->label('')
                            ->options([
                                'alumnos' => '👨‍🎓 Alumnos',
                                'empresas' => '🏢 Empresas',
                            ])
                            ->required()
                            ->live()
                            ->default('alumnos')
                            ->placeholder('Selecciona una opción')
                            ->native(false)
                            ->helperText('Si tienes una lista de estudiantes, elige "Alumnos". Si tienes una lista de organizaciones, elige "Empresas".')
                            ->prefixIcon('heroicon-o-users')
                            ->extraInputAttributes(['class' => 'text-lg font-semibold']),
                    ])
                    ->compact(),

                // ================================================================
                // 🚀 ACCIONES (SOLO DESCARGAR PLANTILLA)
                // ================================================================
                Section::make('Acciones')
                    ->description('Descarga la plantilla para conocer el formato correcto.')
                    ->icon('heroicon-o-play-circle')
                    ->extraAttributes(['class' => 'section-actions'])
                    ->schema([
                        Action::make('descargar_plantilla')
                            ->label('Descargar Plantilla')
                            ->icon('heroicon-o-arrow-down-tray')
                            ->color('success')
                            ->action(function ($livewire) {
                                $tipo = $livewire->data['tipo_importacion'] ?? null;
                                if ($tipo === 'alumnos') {
                                    return redirect()->route('admin.plantilla.alumnos');
                                } elseif ($tipo === 'empresas') {
                                    return redirect()->route('admin.plantilla.empresas');
                                }
                                Notification::make()
                                    ->title('Selecciona un tipo de importación primero')
                                    ->warning()
                                    ->send();
                            })
                            ->extraAttributes(['class' => 'w-full justify-center']),
                    ])
                    ->compact(),

                // ================================================================
                // 📂 SUBE TU ARCHIVO (CON BOTÓN IMPORTAR)
                // ================================================================
                Section::make('Sube tu archivo')
                    ->description('Arrastra o selecciona el archivo Excel o CSV que preparaste, luego impórtalo.')
                    ->icon('heroicon-o-document-arrow-up')
                    ->extraAttributes(['class' => 'section-upload'])
                    ->schema([
                        FileUpload::make('archivo')
                            ->label('')
                            ->acceptedFileTypes([
                                'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                                'application/vnd.ms-excel',
                                'text/csv',
                            ])
                            ->maxSize(5120)
                            ->required()
                            ->helperText('📌 Formatos permitidos: .xlsx, .xls, .csv (Máximo 5MB)')
                            ->disk('local')
                            ->directory('imports')
                            ->placeholder('📂 Arrastra tu archivo aquí o haz clic para seleccionar')
                            ->hint('💡 Usa la plantilla descargada como guía para evitar errores')
                            ->uploadingMessage('⏳ Subiendo archivo...')
                            ->extraAttributes(['class' => 'border-2 border-dashed border-gray-300 dark:border-gray-600 rounded-xl p-4 bg-gray-50 dark:bg-gray-800/30']),

                        // 👇 BOTÓN IMPORTAR AQUÍ
                        Action::make('importar')
                            ->label('Importar')
                            ->icon('heroicon-o-cloud-arrow-up')
                            ->color('primary')
                            ->action('importar')
                            ->submit('importar')
                            ->extraAttributes(['class' => 'w-full justify-center mt-4'])
                            ->disabled(fn ($livewire) => empty($livewire->data['archivo']))
                            ->requiresConfirmation()
                            ->modalHeading('Confirmar importación')
                            ->modalDescription('¿Estás seguro de importar los datos? Esta acción no se puede deshacer.')
                            ->modalSubmitActionLabel('Sí, importar'),
                    ])
                    ->compact(),
            ])
            ->statePath('data');
    }

    public function importar()
    {
        $data = $this->form->getState();

        if (empty($data['tipo_importacion']) || empty($data['archivo'])) {
            Notification::make()
                ->title('Error')
                ->body('Debes seleccionar un tipo de importación y un archivo.')
                ->danger()
                ->send();
            return;
        }

        try {
            $archivo = Storage::disk('local')->path($data['archivo']);

            if (!file_exists($archivo)) {
                Notification::make()
                    ->title('Error')
                    ->body('No se pudo encontrar el archivo.')
                    ->danger()
                    ->send();
                return;
            }

            if ($data['tipo_importacion'] === 'alumnos') {
                $import = new UsersImport();
                Excel::import($import, $archivo);

                $mensaje = "Se importaron {$import->getImportedCount()} alumnos correctamente.";
                if (count($import->getErrors()) > 0) {
                    $mensaje .= "\n\nErrores:\n- " . implode("\n- ", $import->getErrors());
                }

                Notification::make()
                    ->title('Importación completada')
                    ->body($mensaje)
                    ->success()
                    ->send();

            } elseif ($data['tipo_importacion'] === 'empresas') {
                $import = new EmpresasImport();
                Excel::import($import, $archivo);

                $mensaje = "Se importaron {$import->getImportedCount()} empresas correctamente.";
                if (count($import->getErrors()) > 0) {
                    $mensaje .= "\n\nErrores:\n- " . implode("\n- ", $import->getErrors());
                }

                Notification::make()
                    ->title('Importación completada')
                    ->body($mensaje)
                    ->success()
                    ->send();
            }

            $this->form->fill([
                'tipo_importacion' => 'alumnos',
                'archivo' => null,
            ]);

        } catch (\Exception $e) {
            Notification::make()
                ->title('Error en la importación')
                ->body('Ocurrió un error: ' . $e->getMessage())
                ->danger()
                ->send();
        }
    }

    protected function getFormActions(): array
    {
        return [];
    }
}