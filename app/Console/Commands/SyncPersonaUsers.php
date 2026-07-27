<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Persona;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class SyncPersonaUsers extends Command
{

    protected $signature = 'persona:sync-users';
    protected $description = 'Crea usuarios en users a partir de personas sin user_id';

    public function handle()
    {
        $personas = Persona::whereNull('user_id')->get();

        if ($personas->isEmpty()) {
            $this->info('No hay personas pendientes.');
            return 0;
        }

        $bar = $this->output->createProgressBar($personas->count());
        $bar->start();

        foreach ($personas as $persona) {
            try {
                $persona->crearUsuario();
            } catch (\Exception $e) {
                $this->error("Error con CI {$persona->ci}: " . $e->getMessage());
            }
            $bar->advance();
        }

        $bar->finish();
        $this->newLine();
        $this->info('Sincronización completada.');
        return 0;
    }
}
