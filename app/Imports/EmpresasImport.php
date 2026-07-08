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

    public function collection(Collection $rows)
    {
        foreach ($rows as $row) {
            try {
                $nombre = trim($row['nombre'] ?? '');
                if (empty($nombre)) {
                    $this->errors[] = "❌ Fila con nombre vacío";
                    continue;
                }

                $servicioSocial = $this->parsearSiNo($row['servicio_social'] ?? '');
                $practicas = $this->parsearSiNo($row['practicas'] ?? '');
                $programaDual = $this->parsearSiNo($row['dual'] ?? '');

                $fechaTermino = null;
                if (!empty($row['fecha_termino_convenio'])) {
                    try {
                        $fechaTermino = Carbon::parse($row['fecha_termino_convenio'])->format('Y-m-d');
                    } catch (\Exception $e) {
                        // Fecha inválida, se deja como null
                    }
                }

                $activo = true;
                if ($fechaTermino) {
                    $activo = Carbon::parse($fechaTermino)->isFuture();
                }

                Empresa::updateOrCreate(
                    ['nombre' => $nombre],
                    [
                        'direccion' => $row['direccion'] ?? null,
                        'telefono' => $row['telefono'] ?? null,
                        'contacto' => $row['contacto'] ?? null,
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
                $this->errors[] = "❌ Error con empresa: " . $e->getMessage();
            }
        }
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