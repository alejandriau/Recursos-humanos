<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\Models\Persona;

class ActualizarRutasPersona extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
        // Nombre del comando que ejecutarás por consola
    protected $signature = 'personas:actualizar-rutas';

    protected $description = 'Crea carpetas y rutas para personas existentes sin campo archivo';

    public function handle()
    {
        $personas = Persona::whereNull('archivo')->get();

        if ($personas->isEmpty()) {
            $this->info("No hay personas pendientes por actualizar.");
            return;
        }

        foreach ($personas as $persona) {
            $nombre = Str::slug($persona->nombre);
            $apellidoMat = Str::slug($persona->apellidoMat ?? 'SinApellido');
            $fecha = now()->format('Y-m-d');

            $nombreCarpeta = "{$persona->id}_{$nombre}_{$apellidoMat}_{$fecha}";
            $ruta = "archivos/{$nombreCarpeta}";

            Storage::disk('local')->makeDirectory($ruta);

            $persona->archivo = $ruta;
            $persona->save();

            $this->info("Ruta creada: $ruta para ID {$persona->id}");
        }

        $this->info("✔ Carpetas creadas y rutas actualizadas para " . $personas->count() . " personas.");
    }



    /**
     * The console command description.
     *
     * @var string
     */
    

    /**
     * Execute the console command.
     */

}
