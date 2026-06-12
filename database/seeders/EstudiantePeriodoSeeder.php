<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use App\Models\User;
use App\Models\Periodo;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EstudiantePeriodoSeeder extends Seeder
{
    public function run(): void
    {
        // Obtener el periodo activo (el que tiene activo = true)
        $periodoActivo = Periodo::where('activo', true)->first();

        if (!$periodoActivo) {
            $this->command->error('No hay un periodo activo. Ejecuta primero PeriodoSeeder.');
            return;
        }

        // Asignar el periodo activo a todos los estudiantes que no tienen periodo
        $estudiantes = User::where('role', 'estudiante')->get();

        foreach ($estudiantes as $estudiante) {
            // Verificar si ya tiene un periodo activo
            $existe = DB::table('estudiante_periodo')
                ->where('user_id', $estudiante->id)
                ->where('estatus', 'cursando')
                ->exists();

            if (!$existe) {
                DB::table('estudiante_periodo')->insert([
                    'user_id' => $estudiante->id,
                    'periodo_id' => $periodoActivo->id,
                    'estatus' => 'cursando',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                $this->command->info("Asignado periodo {$periodoActivo->nombre} al estudiante {$estudiante->name}");
            }
        }
    }
}