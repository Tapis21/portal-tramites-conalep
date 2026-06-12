<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\Periodo;
use Illuminate\Database\Seeder;

class PeriodoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Periodo::create(['año_inicio' => 2021, 'año_fin' => 2024, 'activo' => false]);
        Periodo::create(['año_inicio' => 2022, 'año_fin' => 2025, 'activo' => false]);
        Periodo::create(['año_inicio' => 2023, 'año_fin' => 2026, 'activo' => true]);
        Periodo::create(['año_inicio' => 2024, 'año_fin' => 2027, 'activo' => false]);
    }
}
