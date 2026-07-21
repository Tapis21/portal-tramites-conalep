<?php

namespace App\Imports;

use App\Models\Empresa;
use Illuminate\Support\Collection;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\SkipsOnError;
use Maatwebsite\Excel\Concerns\SkipsErrors;

class EmpresasImport implements ToCollection, WithHeadingRow, SkipsOnError
{
    use SkipsErrors;

    protected $imported = 0;
    protected $errors = [];
    protected $logs = [];
    protected $rowCount = 0;

    protected $columnAliases = [
        'nombre' => ['nombre', 'Nombre', 'NOMBRE'],
        'direccion' => ['direccion', 'Dirección', 'DIRECCIÓN', 'Direccion'],
        'telefono' => ['telefono', 'Teléfono', 'TELÉFONO', 'Telefono'],
        'contacto' => ['contacto', 'Contacto', 'CONTACTO'],
        'servicio_social' => ['servicio_social', 'Servicio Social', 'SERVICIO SOCIAL'],
        'practicas' => ['practicas', 'Prácticas', 'PRÁCTICAS', 'Practicas'],
        'dual' => ['dual', 'Dual', 'DUAL'],
        'fecha_termino_convenio' => ['fecha_termino_convenio', 'Fecha_Termino_Convenio', 'FECHA_TERMINO_CONVENIO', 'Fecha Termino Convenio'],
    ];

    public function collection(Collection $rows)
    {
        if ($rows->isEmpty()) {
            $this->errors[] = "❌ El archivo está vacío o no contiene datos.";
            return;
        }

        $firstRow = $rows->first();
        $columnMap = $this->getColumnMap($firstRow);

        // ✅ VALIDAR COLUMNA NOMBRE
        if (!isset($columnMap['nombre'])) {
            $this->errors[] = "❌ Columna 'Nombre' no encontrada. Asegúrate de que tu archivo tenga la columna 'Nombre'.";
            return;
        }

        foreach ($rows as $row) {
            $this->rowCount++;

            try {
                $nombre = $this->getColumnValue($row, $columnMap, 'nombre');
                $nombre = $this->sanitizar($nombre);
                
                if (empty($nombre)) {
                    $this->errors[] = "❌ Fila {$this->rowCount}: Nombre de empresa vacío";
                    continue;
                }

                // ✅ PARSEAR SI/NO
                $servicioSocial = $this->parsearSiNo($this->getColumnValue($row, $columnMap, 'servicio_social') ?? '');
                $practicas = $this->parsearSiNo($this->getColumnValue($row, $columnMap, 'practicas') ?? '');
                $programaDual = $this->parsearSiNo($this->getColumnValue($row, $columnMap, 'dual') ?? '');

                // ✅ FECHA DE TÉRMINO
                $fechaTermino = null;
                $fechaStr = $this->getColumnValue($row, $columnMap, 'fecha_termino_convenio');
                if (!empty($fechaStr)) {
                    try {
                        $fechaTermino = Carbon::parse($fechaStr)->format('Y-m-d');
                    } catch (\Exception $e) {
                        $this->errors[] = "⚠️ Fila {$this->rowCount}: Fecha inválida '{$fechaStr}' (debe ser YYYY-MM-DD)";
                    }
                }

                $activo = true;
                if ($fechaTermino) {
                    $activo = Carbon::parse($fechaTermino)->isFuture();
                }

                // ✅ OBTENER VALORES OPCIONALES
                $direccion = $this->sanitizar($this->getColumnValue($row, $columnMap, 'direccion') ?? '');
                $telefono = $this->sanitizar($this->getColumnValue($row, $columnMap, 'telefono') ?? '');
                $contacto = $this->sanitizar($this->getColumnValue($row, $columnMap, 'contacto') ?? '');

                // ✅ CREAR O ACTUALIZAR EMPRESA
                Empresa::updateOrCreate(
                    ['nombre' => $nombre],
                    [
                        'direccion' => $direccion ?? null,
                        'telefono' => $telefono ?? null,
                        'contacto' => $contacto ?? null,
                        'activo' => $activo,
                        'servicio_social' => $servicioSocial,
                        'practicas' => $practicas,
                        'programa_dual' => $programaDual,
                        'fecha_termino_convenio' => $fechaTermino,
                    ]
                );

                $this->imported++;
                $this->logs[] = "✅ Importada: {$nombre}";

            } catch (\Exception $e) {
                $this->errors[] = "❌ Fila {$this->rowCount} - Error: " . $e->getMessage();
            }
        }
    }

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

    private function getColumnValue($row, $columnMap, $key)
    {
        if (isset($columnMap[$key]) && isset($row[$columnMap[$key]])) {
            $value = $row[$columnMap[$key]];
            return !empty($value) ? $value : null;
        }
        return null;
    }

    private function sanitizar($texto)
    {
        if (empty($texto)) return $texto;
        if (!mb_check_encoding($texto, 'UTF-8')) {
            $texto = mb_convert_encoding($texto, 'UTF-8', 'ISO-8859-1');
        }
        $texto = preg_replace('/[\x00-\x1F\x7F]/u', '', $texto);
        return trim($texto);
    }

    private function parsearSiNo($valor)
    {
        if (empty($valor)) return false;
        $valor = strtoupper(trim($valor));
        return in_array($valor, ['SI', 'SÍ', 'YES', 'Y', '1', 'TRUE']);
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