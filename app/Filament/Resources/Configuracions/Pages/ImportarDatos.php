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
use Filament\Schemas\Components\View;
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
                // 🧠 SECCIÓN 1: BIENVENIDA E INTRODUCCIÓN
                // ================================================================
                Section::make(' ¿Qué es esto?')
                    ->description('¿Cansado de dar de alta alumnos uno por uno? Con esta herramienta puedes importar cientos de registros en segundos.')
                    ->icon('heroicon-o-question-mark-circle')
                    ->schema([
                        View::make('filament.components.html-content')
                            ->viewData([
                                'content' => <<<HTML
                                <div class="space-y-3 text-sm">
                                    <div class="flex items-start gap-3 p-3 bg-green-50 dark:bg-green-900/20 rounded-lg border border-green-200 dark:border-green-800">
                                        <span class="text-2xl">📚</span>
                                        <div>
                                            <span class="font-semibold text-green-700 dark:text-green-400">¿Para qué sirve?</span>
                                            <p class="text-gray-600 dark:text-gray-400 text-xs mt-0.5">Esta herramienta está diseñada para que los administradores puedan agregar listas completas de <strong>Alumnos</strong> o <strong>Empresas</strong> al sistema usando un archivo Excel o CSV. En lugar de pasar horas dando de alta uno por uno, subes un archivo y el sistema se encarga de todo.</p>
                                        </div>
                                    </div>
                                    <div class="flex items-start gap-3 p-3 bg-yellow-50 dark:bg-yellow-900/20 rounded-lg border border-yellow-200 dark:border-yellow-800">
                                        <span class="text-2xl">⚡</span>
                                        <div>
                                            <span class="font-semibold text-yellow-700 dark:text-yellow-400">¿Cuánto tiempo ahorras?</span>
                                            <p class="text-gray-600 dark:text-gray-400 text-xs mt-0.5"><strong>1 alumno manual:</strong> 2 minutos → <strong>100 alumnos manuales:</strong> 3 horas 😰</p>
                                            <p class="text-gray-600 dark:text-gray-400 text-xs"><strong>100 alumnos con importación:</strong> 2 minutos 🚀</p>
                                        </div>
                                    </div>
                                    <div class="flex items-start gap-3 p-3 bg-red-50 dark:bg-red-900/20 rounded-lg border border-red-200 dark:border-red-800">
                                        <span class="text-2xl">⚠️</span>
                                        <div>
                                            <span class="font-semibold text-red-700 dark:text-red-400">Antes de empezar</span>
                                            <p class="text-gray-600 dark:text-gray-400 text-xs mt-0.5">Necesitas tener un archivo con el formato correcto. <strong>Descarga la plantilla primero</strong> para ver cómo debe estar organizado tu archivo. No modifiques los nombres de las columnas.</p>
                                        </div>
                                    </div>
                                </div>
                                HTML,
                            ])
                    ])
                    ->collapsible(false)
                    ->compact(),

                // ================================================================
                // 📋 SECCIÓN 2: INSTRUCCIONES PASO A PASO
                // ================================================================
                Section::make(' Instrucciones paso a paso')
                    ->description('Sigue estos 4 pasos simples y tendrás tus datos importados en menos de 5 minutos. ¡No te saltes ninguno!')
                    ->icon('heroicon-o-list-bullet')
                    ->schema([
                        View::make('filament.components.html-content')
                            ->viewData([
                                'content' => <<<HTML
                                <div class="space-y-4 text-sm">
                                    <!-- PASO 1 -->
                                    <div class="flex items-start gap-4 p-3 rounded-lg bg-blue-50 dark:bg-blue-900/20 border-l-4 border-blue-500">
                                        <div class="flex-shrink-0 w-8 h-8 rounded-full bg-blue-500 text-white flex items-center justify-center font-bold text-sm">1</div>
                                        <div>
                                            <span class="font-semibold text-blue-700 dark:text-blue-400">Selecciona el tipo de datos</span>
                                            <p class="text-gray-600 dark:text-gray-400 text-xs mt-0.5">Elige entre <strong>"Alumnos"</strong> (estudiantes) o <strong>"Empresas"</strong> (organizaciones). Esto es importante porque el sistema procesa los datos de forma diferente según el tipo.</p>
                                        </div>
                                    </div>

                                    <!-- PASO 2 -->
                                    <div class="flex items-start gap-4 p-3 rounded-lg bg-green-50 dark:bg-green-900/20 border-l-4 border-green-500">
                                        <div class="flex-shrink-0 w-8 h-8 rounded-full bg-green-500 text-white flex items-center justify-center font-bold text-sm">2</div>
                                        <div>
                                            <span class="font-semibold text-green-700 dark:text-green-400">Descarga la plantilla</span>
                                            <p class="text-gray-600 dark:text-gray-400 text-xs mt-0.5">Haz clic en el botón <strong>"📄 Descargar Plantilla"</strong>. Este archivo te muestra exactamente qué columnas debe tener tu archivo y en qué orden. <span class="text-yellow-600 dark:text-yellow-400">⚠️ No modifiques los nombres de las columnas (primera fila), solo agrega tus datos debajo.</span></p>
                                        </div>
                                    </div>

                                    <!-- PASO 3 -->
                                    <div class="flex items-start gap-4 p-3 rounded-lg bg-yellow-50 dark:bg-yellow-900/20 border-l-4 border-yellow-500">
                                        <div class="flex-shrink-0 w-8 h-8 rounded-full bg-yellow-500 text-white flex items-center justify-center font-bold text-sm">3</div>
                                        <div>
                                            <span class="font-semibold text-yellow-700 dark:text-yellow-400">Llena la plantilla</span>
                                            <p class="text-gray-600 dark:text-gray-400 text-xs mt-0.5">Abre el archivo que descargaste y agrega tus datos en las filas de abajo (debajo de los encabezados). Cada fila es un registro nuevo. <strong>Puedes agregar 1, 100 o 1000 registros</strong> en el mismo archivo.</p>
                                        </div>
                                    </div>

                                    <!-- PASO 4 -->
                                    <div class="flex items-start gap-4 p-3 rounded-lg bg-purple-50 dark:bg-purple-900/20 border-l-4 border-purple-500">
                                        <div class="flex-shrink-0 w-8 h-8 rounded-full bg-purple-500 text-white flex items-center justify-center font-bold text-sm">4</div>
                                        <div>
                                            <span class="font-semibold text-purple-700 dark:text-purple-400">Sube el archivo</span>
                                            <p class="text-gray-600 dark:text-gray-400 text-xs mt-0.5">Arrastra tu archivo al área gris que dice <strong>"Arrastra tu archivo aquí"</strong> o haz clic para seleccionarlo desde tu computadora. Luego presiona el botón <strong>"🚀 Importar"</strong> y el sistema hará el resto.</p>
                                        </div>
                                    </div>

                                    <!-- RESULTADO -->
                                    <div class="flex items-start gap-4 p-3 rounded-lg bg-emerald-50 dark:bg-emerald-900/20 border-l-4 border-emerald-500">
                                        <div class="flex-shrink-0 text-lg">✅</div>
                                        <div>
                                            <span class="font-semibold text-emerald-700 dark:text-emerald-400">¡Listo! Ya terminaste</span>
                                            <p class="text-gray-600 dark:text-gray-400 text-xs mt-0.5">El sistema procesará tu archivo y te mostrará un resumen con la cantidad de registros importados. <strong>Si algún registro ya existía, se actualizará automáticamente</strong> (no se crearán duplicados).</p>
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
                // 🚀 SECCIÓN 3: EL FORMULARIO DE IMPORTACIÓN
                // ================================================================
                Section::make(' ¡Empieza aquí!')
                    ->description('Sigue estos 3 pasos simples: selecciona, sube y importa.')
                    ->icon('heroicon-o-cloud-arrow-up')
                    ->schema([

                        // PASO 1: Seleccionar tipo
                        Section::make('1️⃣ ¿Qué vas a importar?')
                            ->description('Elige el tipo de datos que contiene tu archivo.')
                            ->schema([
                                Select::make('tipo_importacion')
                                    ->label('Tipo de importación')
                                    ->options([
                                        'alumnos' => '👨‍🎓 Alumnos',
                                        'empresas' => '🏢 Empresas',
                                    ])
                                    ->required()
                                    ->live()
                                    ->default('alumnos')
                                    ->placeholder('👆 Selecciona una opción')
                                    ->native(false)
                                    ->helperText('📌 Si estás importando estudiantes, elige "Alumnos". Si estás importando organizaciones, elige "Empresas".'),
                            ])
                            ->compact(),

                        // PASO 2: Subir archivo
                        Section::make('2️⃣ Sube tu archivo')
                            ->description('Arrastra o selecciona el archivo que preparaste.')
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
                                    ->helperText('📄 Formatos permitidos: .xlsx, .xls, .csv (Máximo 5MB)')
                                    ->disk('local')
                                    ->directory('imports')
                                    ->placeholder('📂 Arrastra tu archivo aquí o haz clic para seleccionar')
                                    ->hint('📌 Asegúrate de usar la plantilla descargada')
                                    ->uploadingMessage('📤 Subiendo archivo...'),
                            ])
                            ->compact(),

                        // PASO 3: Acciones finales (SIN GRID)
                        Section::make('3️⃣ ¡Ejecuta la importación!')
                            ->description('Descarga la plantilla si no la tienes, luego importa tu archivo.')
                            ->schema([
                                Action::make('descargar_plantilla')
                                    ->label(' Descargar Plantilla')
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
                                    ->extraAttributes(['style' => 'width: 100%; justify-content: center;']),

                                Action::make('importar')
                                    ->label(' Importar')
                                    ->icon('heroicon-o-cloud-arrow-up')
                                    ->color('primary')
                                    ->action('importar')
                                    ->submit('importar')
                                    ->extraAttributes(['style' => 'width: 100%; justify-content: center;'])
                                    ->disabled(fn ($livewire) => empty($livewire->data['archivo']))
                                    ->requiresConfirmation()
                                    ->modalHeading('Confirmar importación')
                                    ->modalDescription('¿Estás seguro de importar los datos? Esta acción no se puede deshacer.')
                                    ->modalSubmitActionLabel('Sí, importar'),
                            ])
                            ->compact(),
                    ])
                    ->collapsible(false)
                    ->compact(),

                // ================================================================
                // ℹ️ SECCIÓN 4: INFORMACIÓN TÉCNICA DETALLADA
                // ================================================================
                Section::make('ℹ️ Información técnica importante')
                    ->description('Aquí tienes toda la información técnica que necesitas saber antes de importar. Esto te ayudará a entender qué hace el sistema automáticamente y qué esperar después de la importación.')
                    ->icon('heroicon-o-information-circle')
                    ->schema([
                        View::make('filament.components.html-content')
                            ->viewData([
                                'content' => <<<HTML
                                <div class="space-y-4 text-sm">
                                    <!-- Alumnos -->
                                    <div class="bg-blue-50 dark:bg-blue-900/20 p-4 rounded-lg border border-blue-200 dark:border-blue-800">
                                        <div class="flex items-center gap-2 mb-3">
                                            <span class="text-2xl">👨‍🎓</span>
                                            <h4 class="font-bold text-blue-700 dark:text-blue-400">Importación de Alumnos</h4>
                                        </div>
                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                            <div>
                                                <p class="font-semibold text-blue-600 dark:text-blue-400 text-xs">📋 Datos que debes incluir</p>
                                                <ul class="space-y-1 text-gray-600 dark:text-gray-400 text-xs mt-1">
                                                    <li>• <strong>Matrícula</strong> (única, no se puede repetir)</li>
                                                    <li>• <strong>Nombre(s)</strong></li>
                                                    <li>• <strong>Primer apellido</strong></li>
                                                    <li>• <strong>Segundo apellido</strong> (opcional)</li>
                                                    <li>• <strong>Grupo Referente</strong> (ej: 601-ADMO23)</li>
                                                    <li>• <strong>Periodo</strong> (ej: 2023-2026)</li>
                                                </ul>
                                            </div>
                                            <div>
                                                <p class="font-semibold text-blue-600 dark:text-blue-400 text-xs">🤖 Lo que el sistema hace por ti</p>
                                                <ul class="space-y-1 text-gray-600 dark:text-gray-400 text-xs mt-1">
                                                    <li>• 📧 <strong>Email automático:</strong> matricula@conalepqroo.edu.mx</li>
                                                    <li>• 🔑 <strong>Contraseña:</strong> Su matrícula</li>
                                                    <li>• 📌 <strong>Semestre:</strong> Se extrae del grupo (ej: 601 → 6to)</li>
                                                    <li>• 📌 <strong>Carrera:</strong> Se extrae del grupo (ej: ADMO → Administración)</li>
                                                    <li>• 🕐 <strong>Turno:</strong> Se asigna según el número de grupo (101-107 = Matutino, 108-114 = Vespertino)</li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Empresas -->
                                    <div class="bg-green-50 dark:bg-green-900/20 p-4 rounded-lg border border-green-200 dark:border-green-800">
                                        <div class="flex items-center gap-2 mb-3">
                                            <span class="text-2xl">🏢</span>
                                            <h4 class="font-bold text-green-700 dark:text-green-400">Importación de Empresas</h4>
                                        </div>
                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                            <div>
                                                <p class="font-semibold text-green-600 dark:text-green-400 text-xs">📋 Datos que debes incluir</p>
                                                <ul class="space-y-1 text-gray-600 dark:text-gray-400 text-xs mt-1">
                                                    <li>• <strong>Nombre</strong> (obligatorio)</li>
                                                    <li>• <strong>Dirección</strong> (opcional)</li>
                                                    <li>• <strong>Teléfono</strong> (opcional)</li>
                                                    <li>• <strong>Contacto</strong> (email, opcional)</li>
                                                    <li>• <strong>Servicio Social</strong> (SI/NO)</li>
                                                    <li>• <strong>Prácticas</strong> (SI/NO)</li>
                                                    <li>• <strong>Dual</strong> (SI/NO)</li>
                                                    <li>• <strong>Fecha_Termino_Convenio</strong> (YYYY-MM-DD)</li>
                                                </ul>
                                            </div>
                                            <div>
                                                <p class="font-semibold text-green-600 dark:text-green-400 text-xs">🤖 Lo que el sistema hace por ti</p>
                                                <ul class="space-y-1 text-gray-600 dark:text-gray-400 text-xs mt-1">
                                                    <li>• 📅 <strong>Campo "activo":</strong> Se actualiza automáticamente según la fecha de término</li>
                                                    <li>• 🔄 <strong>Actualización:</strong> Si el nombre ya existe, se actualiza la empresa</li>
                                                    <li>• 📌 <strong>Fecha:</strong> Debe estar en formato YYYY-MM-DD</li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Solución de problemas -->
                                    <div class="p-4 bg-orange-50 dark:bg-orange-900/20 rounded-lg border border-orange-200 dark:border-orange-800">
                                        <div class="flex items-center gap-2 mb-2">
                                            <span class="text-xl">🛠️</span>
                                            <h4 class="font-semibold text-orange-700 dark:text-orange-400">Solución de problemas comunes</h4>
                                        </div>
                                        <ul class="text-xs text-gray-600 dark:text-gray-400 space-y-1">
                                            <li>❌ <strong>"No se encontró el archivo"</strong> → Asegúrate de que el archivo no esté abierto en otro programa mientras lo subes.</li>
                                            <li>❌ <strong>"Formato no permitido"</strong> → Guarda tu archivo como .xlsx, .xls o .csv.</li>
                                            <li>❌ <strong>"El archivo pesa más de 5MB"</strong> → Reduce el tamaño o divide tu lista en varios archivos.</li>
                                            <li>❌ <strong>"Fila incompleta"</strong> → Revisa que todas las columnas tengan datos (especialmente Matrícula y Nombre).</li>
                                            <li>❌ <strong>"Matrícula duplicada"</strong> → El sistema actualizará al alumno existente en lugar de crear uno nuevo.</li>
                                        </ul>
                                    </div>

                                    <div class="p-3 bg-gray-100 dark:bg-gray-800 rounded-lg text-center text-xs text-gray-500 dark:text-gray-400">
                                        💡 <strong>Consejo:</strong> Si tienes muchos datos, importa primero un grupo pequeño (5-10 registros) para verificar que todo funcione antes de importar la lista completa.
                                    </div>
                                </div>
                                HTML,
                            ])
                    ])
                    ->collapsible(true)
                    ->collapsed(true)
                    ->compact(),

                // ================================================================
                // 📊 SECCIÓN 5: PREVISUALIZACIÓN (Futura mejora)
                // ================================================================
                Section::make('🔮 Vista previa de importación (Próximamente)')
                    ->description('En futuras versiones, podrás ver una vista previa de los datos antes de importarlos.')
                    ->icon('heroicon-o-eye')
                    ->schema([
                        View::make('filament.components.html-content')
                            ->viewData([
                                'content' => <<<HTML
                                <div class="text-center text-sm text-gray-500 dark:text-gray-400 p-4">
                                    <span class="text-3xl">🚧</span>
                                    <p class="mt-2">Estamos trabajando en una función que te permitirá:</p>
                                    <ul class="text-xs space-y-1 mt-1">
                                        <li>✅ Ver los datos antes de importarlos</li>
                                        <li>✅ Detectar errores antes de subir el archivo</li>
                                        <li>✅ Confirmar la importación con un solo clic</li>
                                    </ul>
                                    <p class="text-xs text-gray-400 mt-2">Esta función estará disponible pronto. ¡Mantente atento!</p>
                                </div>
                                HTML,
                            ])
                    ])
                    ->collapsible(true)
                    ->collapsed(true)
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
                    ->body('No se pudo encontrar el archivo. Verifica que se haya subido correctamente.')
                    ->danger()
                    ->send();
                return;
            }

            if ($data['tipo_importacion'] === 'alumnos') {
                $import = new UsersImport();
                Excel::import($import, $archivo);

                $mensaje = "✅ Se importaron {$import->getImportedCount()} alumnos correctamente.";
                if (count($import->getErrors()) > 0) {
                    $mensaje .= "\n\n⚠️ Errores:\n- " . implode("\n- ", $import->getErrors());
                }

                Notification::make()
                    ->title('Importación completada')
                    ->body($mensaje)
                    ->success()
                    ->send();

            } elseif ($data['tipo_importacion'] === 'empresas') {
                $import = new EmpresasImport();
                Excel::import($import, $archivo);

                $mensaje = "✅ Se importaron {$import->getImportedCount()} empresas correctamente.";
                if (count($import->getErrors()) > 0) {
                    $mensaje .= "\n\n⚠️ Errores:\n- " . implode("\n- ", $import->getErrors());
                }

                Notification::make()
                    ->title('Importación completada')
                    ->body($mensaje)
                    ->success()
                    ->send();
            }

            $this->form->fill([
                'tipo_importacion' => 'alumnos',
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