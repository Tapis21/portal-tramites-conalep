<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use Illuminate\Support\Facades\DB;

class TurnoHorarioSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Turnos
        DB::table('turnos')->insert([
            ['nombre' => 'matutino'],
            ['nombre' => 'vespertino'],
        ]);

        // Horarios para turno matutino (estudiante de mañana → servicio en tarde)
        DB::table('horarios')->insert([
            ['turno_id' => 1, 'hora_inicio' => '14:00', 'hora_fin' => '18:00'],
            ['turno_id' => 1, 'hora_inicio' => '15:00', 'hora_fin' => '19:00'],
            ['turno_id' => 1, 'hora_inicio' => '16:00', 'hora_fin' => '20:00'],
            // Horarios para turno vespertino (estudiante de tarde → servicio en mañana)
            ['turno_id' => 2, 'hora_inicio' => '07:00', 'hora_fin' => '11:00'],
            ['turno_id' => 2, 'hora_inicio' => '08:00', 'hora_fin' => '12:00'],
            ['turno_id' => 2, 'hora_inicio' => '09:00', 'hora_fin' => '13:00'],
        ]);
    }
}
