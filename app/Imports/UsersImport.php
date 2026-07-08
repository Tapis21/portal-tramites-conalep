<?php

namespace App\Imports;

use App\Models\User;
use App\Models\Periodo;
use App\Models\EstudiantePeriodo;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\SkipsOnError;
use Maatwebsite\Excel\Concerns\SkipsErrors;

class UsersImport implements ToCollection, WithHeadingRow, SkipsOnError
{
    use SkipsErrors;

    protected $imported = 0;
    protected $errors = [];
    protected $logs = [];

    // Mapeo de códigos de carrera
    protected $carreras = [
        'ADMO' => 'Administración',
        'INFO' => 'Informática',
        'CDIA' => 'Ciencia de Datos e Inteligencia Artificial',
        'EGAD' => 'Expresión Gráfica Digital',
    ];

    public function collection(Collection $rows)
    {
        foreach ($rows as $row) {
            try {
                // Validar campos requeridos
                if (empty($row['matricula']) || empty($row['nombre']) || empty($row['primer_apellido'])) {
                    $this->errors[] = "❌ Fila incompleta: matrícula {$row['matricula']}";
                    continue;
                }

                // Extraer datos del grupo
                $grupoReferente = $row['grupo_referente'] ?? '';
                $semestre = $this->extraerSemestre($grupoReferente);
                $carrera = $this->extraerCarrera($grupoReferente);
                $turnoId = $this->extraerTurno($grupoReferente);

                // Generar email y password
                $email = $this->generarEmail($row['matricula']);
                $password = Hash::make($row['matricula']);

                // Crear o actualizar usuario
                $user = User::updateOrCreate(
                    ['matricula' => $row['matricula']],
                    [
                        'name' => trim($row['nombre']),
                        'apellidos' => trim($row['primer_apellido'] . ' ' . ($row['segundo_apellido'] ?? '')),
                        'email' => $email,
                        'password' => $password,
                        'role' => 'estudiante',
                        'semestre' => $semestre,
                        'grupo' => $grupoReferente,
                        'carrera' => $carrera,
                        'turno_id' => $turnoId,
                        'estatus_servicio_social' => 'no_solicitado',
                        'estatus_practicas' => 'no_solicitado',
                    ]
                );

                // ================================================================
                // 🔥 ASIGNACIÓN DE PERIODO (TABLA estudiante_periodo)
                // ================================================================
                $periodoNombre = $row['periodo'] ?? null;
                if (!empty($periodoNombre)) {
                    $periodo = $this->obtenerOCrearPeriodo($periodoNombre);
                    
                    if ($periodo) {
                        // Verificar si ya tiene este periodo asignado
                        $existe = EstudiantePeriodo::where('user_id', $user->id)
                            ->where('periodo_id', $periodo->id)
                            ->exists();
                        
                        if (!$existe) {
                            EstudiantePeriodo::create([
                                'user_id' => $user->id,
                                'periodo_id' => $periodo->id,
                                'estatus' => 'cursando',
                            ]);
                        }
                    }
                }

                $this->imported++;
                $this->logs[] = "✅ Importado: {$row['matricula']} - {$row['nombre']}";

            } catch (\Exception $e) {
                $this->errors[] = "❌ Error con {$row['matricula']}: " . $e->getMessage();
            }
        }
    }

    // ================================================================
    // 🔥 MÉTODO PARA OBTENER O CREAR PERIODO
    // ================================================================
    private function obtenerOCrearPeriodo($nombrePeriodo)
    {
        if (empty($nombrePeriodo)) return null;

        // Buscar el periodo en la base de datos
        $periodo = Periodo::where('nombre', $nombrePeriodo)->first();

        // Si no existe, lo creamos
        if (!$periodo) {
            // Extraer años del nombre (ej: "2023-2026")
            preg_match('/(\d{4})-(\d{4})/', $nombrePeriodo, $matches);
            
            if (!empty($matches)) {
                $añoInicio = $matches[1];
                $añoFin = $matches[2];
                
                $periodo = Periodo::create([
                    'año_inicio' => $añoInicio,
                    'año_fin' => $añoFin,
                    'nombre' => $nombrePeriodo,
                    'activo' => false, // Por defecto no activo (el admin lo activará)
                ]);
            } else {
                // Si el formato no es válido
                $this->errors[] = "❌ Formato de periodo inválido: {$nombrePeriodo} (debe ser YYYY-YYYY)";
                return null;
            }
        }

        return $periodo;
    }

    // ================================================================
    // 🔥 MÉTODOS AUXILIARES
    // ================================================================

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

    public function getImportedCount()
    {
        return $this->imported;
    }

    public function getErrors()
    {
        return $this->errors;
    }

    public function getLogs()
    {
        return $this->logs;
    }
}