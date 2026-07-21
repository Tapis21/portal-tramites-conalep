<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class FixStoragePermissions extends Command
{
    protected $signature = 'storage:fix';
    protected $description = 'Fija los permisos de storage y crea el enlace simbólico';

    public function handle()
    {
        $this->info('🔧 Arreglando permisos de storage...');

        // 1. Crear enlace simbólico
        $this->call('storage:link');

        // 2. Detectar sistema operativo
        if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
            // Windows
            $this->info('🪟 Sistema Windows detectado');
            $this->info('Ejecuta manualmente: icacls "'.base_path('storage').'" /grant Everyone:F /T');
            $this->info('O desde PowerShell como admin: icacls "'.base_path('storage').'" /grant Everyone:F /T');
        } else {
            // Linux/Mac
            $this->info('🐧 Sistema Linux/Mac detectado');
            
            $paths = [
                'storage',
                'bootstrap/cache',
                'public/storage',
            ];
            
            foreach ($paths as $path) {
                $fullPath = base_path($path);
                if (is_dir($fullPath)) {
                    exec("chmod -R 775 {$fullPath}");
                    $this->line("✅ Permisos fijados en: {$path}");
                }
            }
            
            // Cambiar propietario
            $webUser = 'www-data'; // Cambiar según el servidor
            exec("chown -R {$webUser}:{$webUser} " . base_path('storage'));
            exec("chown -R {$webUser}:{$webUser} " . base_path('public/storage'));
            $this->info("✅ Propietario cambiado a: {$webUser}");
        }

        $this->info('✅ ¡Todo listo! Los permisos de storage están configurados correctamente.');
    }
}