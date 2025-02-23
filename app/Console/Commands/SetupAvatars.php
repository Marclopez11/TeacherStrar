<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class SetupAvatars extends Command
{
    protected $signature = 'avatars:setup';
    protected $description = 'Setup initial avatars';

    public function handle()
    {
        $avatarsDir = public_path('images/avatars');
        $resourceAvatarsDir = resource_path('avatars');

        // Crear el directorio si no existe
        if (!File::exists($avatarsDir)) {
            File::makeDirectory($avatarsDir, 0755, true);
        }

        // Copiar Avatar_0.svg como default.svg si no existe
        if (!File::exists($avatarsDir . '/default.svg')) {
            File::copy(
                $resourceAvatarsDir . '/Avatar_0.svg',
                $avatarsDir . '/default.svg'
            );
            $this->info('Default avatar created.');
        }

        // Copiar todos los avatares del 0 al 45
        $count = 0;
        for ($i = 0; $i <= 45; $i++) {
            $sourceFile = $resourceAvatarsDir . "/Avatar_{$i}.svg";
            $destFile = $avatarsDir . "/Avatar_{$i}.svg";

            if (File::exists($sourceFile) && !File::exists($destFile)) {
                File::copy($sourceFile, $destFile);
                $count++;
            }
        }

        $this->info("{$count} avatars copied successfully!");
        $this->info('Avatars setup completed!');
    }
}
