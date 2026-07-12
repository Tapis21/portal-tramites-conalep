<?php

namespace App\Filament\Resources\Users\Pages;

use App\Filament\Resources\Users\UserResource;
use App\Models\EstudiantePeriodo;
use Filament\Resources\Pages\ListRecords;
use Filament\Actions\Action;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Hidden;
use Illuminate\Support\Facades\Hash;
use App\Models\Periodo;
use App\Models\User;
use Filament\Notifications\Notification;

class ListUsers extends ListRecords
{
    protected static string $resource = UserResource::class;

    protected $carreras = [
        'ADMO' => 'Administración',
        'INFO' => 'Informática',
        'CDIA' => 'Ciencia de Datos e Inteligencia Artificial',
        'EGAD' => 'Expresión Gráfica Digital',
    ];

    protected function getHeaderActions(): array
    {
        return [
            Action::make('crear_user')
                ->label('Crear user')
                ->icon('heroicon-o-plus-circle')
                ->color('success')
                ->form([
                    // ✅ ROL (PRIMERO)
                    Select::make('role')
                        ->label('Rol')
                        ->options([
                            'estudiante' => 'Estudiante',
                            'admin' => 'Administrador',
                        ])
                        ->required()
                        ->default('estudiante')
                        ->live()
                        ->reactive()
                        ->afterStateUpdated(function ($state, $set, $get) {
                            // Limpiar campos ocultos al cambiar de rol
                            if ($state === 'admin') {
                                $set('matricula', null);
                                $set('grupo_referente', null);
                                $set('periodo_id', null);
                            }
                        }),

                    // ============================================================
                    // 👨‍🎓 CAMPOS PARA ESTUDIANTE
                    // ============================================================
                    TextInput::make('matricula')
                        ->label('Matrícula')
                        ->required()
                        ->unique('users', 'matricula')
                        ->helperText('La matrícula se usará como contraseña por defecto')
                        ->visible(fn ($get) => $get('role') === 'estudiante'),

                    TextInput::make('nombre')
                        ->label('Nombre(s)')
                        ->required(),

                    TextInput::make('primer_apellido')
                        ->label('Primer apellido')
                        ->required(),

                    TextInput::make('segundo_apellido')
                        ->label('Segundo apellido')
                        ->helperText('Opcional'),

                    TextInput::make('grupo_referente')
                        ->label('Grupo Referente')
                        ->required()
                        ->placeholder('Ej: 601-ADMO23')
                        ->helperText('El sistema extraerá semestre, carrera y turno automáticamente')
                        ->visible(fn ($get) => $get('role') === 'estudiante'),

                    Select::make('periodo_id')
                        ->label('Periodo')
                        ->options(
                            Periodo::where('activo', true)
                                ->orderBy('año_inicio', 'desc')
                                ->pluck('nombre', 'id')
                                ->toArray()
                        )
                        ->placeholder('Selecciona un periodo')
                        ->helperText('Periodo actual del estudiante')
                        ->required()
                        ->visible(fn ($get) => $get('role') === 'estudiante'),

                    // ============================================================
                    // 👨‍💼 CAMPOS PARA ADMIN
                    // ============================================================
                    TextInput::make('email_admin')
                        ->label('Email')
                        ->email()
                        ->required()
                        ->unique('users', 'email')
                        ->helperText('El email del administrador')
                        ->visible(fn ($get) => $get('role') === 'admin'),

                    TextInput::make('password_admin')
                        ->label('Contraseña')
                        ->password()
                        ->required()
                        ->minLength(8)
                        ->helperText('Mínimo 8 caracteres')
                        ->visible(fn ($get) => $get('role') === 'admin'),

                    // ✅ CAMPOS COMUNES (siempre visibles)
                    Hidden::make('email')
                        ->default(null),

                    Hidden::make('password')
                        ->default(null),
                ])
                ->action(function (array $data) {
                    $role = $data['role'];

                    if ($role === 'estudiante') {
                        // ✅ CREAR ESTUDIANTE (misma lógica que importación)
                        $grupo = $data['grupo_referente'];
                        $semestre = $this->extraerSemestre($grupo);
                        $carrera = $this->extraerCarrera($grupo);
                        $turnoId = $this->extraerTurno($grupo);
                        $email = $this->generarEmail($data['matricula']);
                        $password = Hash::make($data['matricula']);

                        $user = User::create([
                            'matricula' => $data['matricula'],
                            'name' => trim($data['nombre']),
                            'apellidos' => trim($data['primer_apellido'] . ' ' . ($data['segundo_apellido'] ?? '')),
                            'email' => $email,
                            'password' => $password,
                            'role' => 'estudiante',
                            'semestre' => $semestre,
                            'grupo' => $grupo,
                            'carrera' => $carrera,
                            'turno_id' => $turnoId,
                            'estatus_servicio_social' => 'no_solicitado',
                            'estatus_practicas' => 'no_solicitado',
                        ]);

                        // Asignar periodo
                        if (isset($data['periodo_id']) && $data['periodo_id']) {
                            EstudiantePeriodo::create([
                                'user_id' => $user->id,
                                'periodo_id' => $data['periodo_id'],
                                'estatus' => 'cursando',
                            ]);
                        }

                        Notification::make()
                            ->title('✅ Estudiante creado')
                            ->body("Estudiante {$data['nombre']} creado. Contraseña: {$data['matricula']}")
                            ->success()
                            ->send();

                    } elseif ($role === 'admin') {
                        // ✅ CREAR ADMIN
                        $user = User::create([
                            'name' => trim($data['nombre']),
                            'apellidos' => trim($data['primer_apellido'] . ' ' . ($data['segundo_apellido'] ?? '')),
                            'email' => $data['email_admin'],
                            'password' => Hash::make($data['password_admin']),
                            'role' => 'admin',
                            'matricula' => 'ADMIN' . str_pad(User::count() + 1, 3, '0', STR_PAD_LEFT),
                            'estatus_servicio_social' => 'no_solicitado',
                            'estatus_practicas' => 'no_solicitado',
                        ]);

                        Notification::make()
                            ->title('✅ Administrador creado')
                            ->body("Administrador {$data['nombre']} creado correctamente.")
                            ->success()
                            ->send();
                    }

                    $this->dispatch('refresh-table');
                })
                ->modalHeading('Crear nuevo usuario')
                ->modalDescription('Selecciona el rol y completa los campos correspondientes.')
                ->modalSubmitActionLabel('Crear usuario')
                ->modalCancelActionLabel('Cancelar')
                ->modalWidth('2xl'),
        ];
    }

    // ✅ MÉTODOS AUXILIARES (igual que en UsersImport)

    private function extraerSemestre($grupo)
    {
        if (empty($grupo)) return null;
        preg_match('/^(\d+)/', $grupo, $matches);
        if (!empty($matches[1])) {
            $semestre = (int) substr($matches[1], 0, 1);
            return $semestre >= 1 && $semestre <= 6 ? $semestre : null;
        }
        return null;
    }

    private function extraerCarrera($grupo)
    {
        if (empty($grupo)) return '';
        preg_match('/-([A-Z]+)/', $grupo, $matches);
        if (!empty($matches[1])) {
            return $this->carreras[$matches[1]] ?? $matches[1];
        }
        return '';
    }

    private function extraerTurno($grupo)
    {
        if (empty($grupo)) return 2;
        preg_match('/^(\d+)/', $grupo, $matches);
        if (!empty($matches[1])) {
            $numero = (int) $matches[1];
            $ultimoDigito = $numero % 100;
            if ($ultimoDigito >= 1 && $ultimoDigito <= 7) {
                return 1; // Matutino
            } elseif ($ultimoDigito >= 8 && $ultimoDigito <= 14) {
                return 2; // Vespertino
            }
        }
        return 2;
    }

    private function generarEmail($matricula)
    {
        $matriculaLimpia = preg_replace('/[^a-zA-Z0-9]/', '', $matricula);
        return strtolower($matriculaLimpia) . '@conalepqroo.edu.mx';
    }
}