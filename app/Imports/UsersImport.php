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
use Maatwebsite\Excel\Concerns\WithValidation;

class UsersImport implements ToCollection, WithHeadingRow, SkipsOnError
{
    use SkipsErrors;

    protected $imported = 0;
    protected $errors = [];
    protected $logs = [];
    protected $rowCount = 0;

    // ✅ Mapeo de códigos de carrera
    protected $carreras = [
        'ADMO' => 'Administración',
        'INFO' => 'Informática',
        'CDIA' => 'Ciencia de Datos e Inteligencia Artificial',
        'EGAD' => 'Expresión Gráfica Digital',
    ];

    // ✅ COLUMNAS REQUERIDAS (con y sin acento)
    protected $requiredColumns = ['matricula', 'nombre', 'primer_apellido', 'grupo_referente', 'periodo'];
    protected $columnAliases = [
        'matricula' => ['matricula', 'Matrícula', 'MATRÍCULA', 'Matricula'],
        'nombre' => ['nombre', 'Nombre', 'NOMBRE'],
        'primer_apellido' => ['primer_apellido', 'Primer apellido', 'Primer Apellido', 'PRIMER APELLIDO'],
        'segundo_apellido' => ['segundo_apellido', 'Segundo apellido', 'Segundo Apellido', 'SEGUNDO APELLIDO'],
        'grupo_referente' => ['grupo_referente', 'Grupo Referente', 'GRUPO REFERENTE', 'grupo referente'],
        'periodo' => ['periodo', 'Periodo', 'PERIODO'],
    ];

    public function collection(Collection $rows)
    {
        if ($rows->isEmpty()) {
            $this->errors[] = "❌ El archivo está vacío o no contiene datos.";
            return;
        }

        // ✅ OBTENER NOMBRES REALES DE COLUMNAS
        $firstRow = $rows->first();
        $columnMap = $this->getColumnMap($firstRow);

        // ✅ VALIDAR COLUMNAS REQUERIDAS
        $missingColumns = [];
        foreach ($this->requiredColumns as $column) {
            if (!isset($columnMap[$column])) {
                $missingColumns[] = $column;
            }
        }

        if (!empty($missingColumns)) {
            $this->errors[] = "❌ Columnas faltantes en el archivo: " . implode(', ', $missingColumns);
            $this->errors[] = "📌 Asegúrate de que tu archivo tenga las columnas: Matrícula, Nombre, Primer apellido, Grupo Referente, Periodo";
            return;
        }

        foreach ($rows as $row) {
            $this->rowCount++;

            try {
                // ✅ OBTENER VALORES USANDO EL MAPEO DE COLUMNAS
                $matricula = $this->getColumnValue($row, $columnMap, 'matricula');
                $nombre = $this->getColumnValue($row, $columnMap, 'nombre');
                $primerApellido = $this->getColumnValue($row, $columnMap, 'primer_apellido');
                $segundoApellido = $this->getColumnValue($row, $columnMap, 'segundo_apellido');
                $grupoReferente = $this->getColumnValue($row, $columnMap, 'grupo_referente');
                $periodoNombre = $this->getColumnValue($row, $columnMap, 'periodo');

                if (empty($matricula) || empty($nombre) || empty($primerApellido)) {
                    $this->errors[] = "❌ Fila {$this->rowCount}: Faltan datos obligatorios (Matrícula, Nombre o Primer apellido)";
                    continue;
                }

                // ✅ LIMPIAR Y NORMALIZAR DATOS (UTF-8)
                $matricula = $this->sanitizar($matricula);
                $nombre = $this->sanitizar($nombre);
                $primerApellido = $this->sanitizar($primerApellido);
                $segundoApellido = $this->sanitizar($segundoApellido);
                $grupoReferente = $this->sanitizar($grupoReferente);
                $periodoNombre = $this->sanitizar($periodoNombre);

                // ✅ EXTRAER DATOS DEL GRUPO
                $semestre = $this->extraerSemestre($grupoReferente);
                $carrera = $this->extraerCarrera($grupoReferente);
                $turnoId = $this->extraerTurno($grupoReferente);

                // ✅ GENERAR EMAIL Y PASSWORD
                $email = $this->generarEmail($matricula);
                $password = Hash::make($matricula);

                // ✅ CREAR O ACTUALIZAR USUARIO
                $user = User::updateOrCreate(
                    ['matricula' => $matricula],
                    [
                        'name' => trim($nombre),
                        'apellidos' => trim($primerApellido . ' ' . ($segundoApellido ?? '')),
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

                // ✅ ASIGNACIÓN DE PERIODO
                if (!empty($periodoNombre)) {
                    $periodo = $this->obtenerOCrearPeriodo($periodoNombre);
                    
                    if ($periodo) {
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
                $this->logs[] = "✅ Importado: {$matricula} - {$nombre}";

            } catch (\Exception $e) {
                $this->errors[] = "❌ Fila {$this->rowCount} - Error: " . $e->getMessage();
            }
        }
    }

    // ✅ OBTENER MAPEO DE COLUMNAS DEL ARCHIVO
    private function getColumnMap($row)
    {
        $map = [];
        $rowKeys = array_keys($row->toArray());
        
        foreach ($this->columnAliases as $key => $aliases) {
            foreach ($aliases as $alias) {
                if (in_array($alias, $rowKeys)) {
                    $map[$key] = $alias;
                    break;
                }
            }
        }
        
        return $map;
    }

    // ✅ OBTENER VALOR DE COLUMNA USANDO EL MAPEO
    private function getColumnValue($row, $columnMap, $key)
    {
        if (isset($columnMap[$key]) && isset($row[$columnMap[$key]])) {
            $value = $row[$columnMap[$key]];
            return !empty($value) ? $value : null;
        }
        return null;
    }

    // ✅ SANITIZAR TEXTO (UTF-8)
    private function sanitizar($texto)
    {
        if (empty($texto)) return $texto;
        // Convertir a UTF-8 si no lo está
        if (!mb_check_encoding($texto, 'UTF-8')) {
            $texto = mb_convert_encoding($texto, 'UTF-8', 'ISO-8859-1');
        }
        // Eliminar caracteres de control
        $texto = preg_replace('/[\x00-\x1F\x7F]/u', '', $texto);
        return trim($texto);
    }

    private function obtenerOCrearPeriodo($nombrePeriodo)
    {
        if (empty($nombrePeriodo)) return null;

        $periodo = Periodo::where('nombre', $nombrePeriodo)->first();

        if (!$periodo) {
            preg_match('/(\d{4})-(\d{4})/', $nombrePeriodo, $matches);
            
            if (!empty($matches)) {
                $añoInicio = $matches[1];
                $añoFin = $matches[2];
                
                $periodo = Periodo::create([
                    'año_inicio' => $añoInicio,
                    'año_fin' => $añoFin,
                    'nombre' => $nombrePeriodo,
                    'activo' => false,
                ]);
            } else {
                $this->errors[] = "❌ Formato de periodo inválido: {$nombrePeriodo} (debe ser YYYY-YYYY)";
                return null;
            }
        }

        return $periodo;
    }

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
        $matriculaLimpia = preg_replace('/[^a-zA-Z0-9-]/', '', $matricula);
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