<?php
namespace Database\Seeders;
// database/seeders/ConfigDiasVacacionSeeder.php
use Illuminate\Database\Seeder;
use App\Models\ConfigDiasVacacion;

class ConfigDiasVacacionSeeder extends Seeder
{
    public function run()
    {
        ConfigDiasVacacion::create([
            'anios_desde' => 1,
            'anios_hasta' => 4,
            'dias' => 15,
            'descripcion' => '1 a 4 años',
            'activo' => true,
        ]);

        ConfigDiasVacacion::create([
            'anios_desde' => 5,
            'anios_hasta' => 9,
            'dias' => 20,
            'descripcion' => '5 a 9 años',
            'activo' => true,
        ]);

        ConfigDiasVacacion::create([
            'anios_desde' => 10,
            'anios_hasta' => null,
            'dias' => 30,
            'descripcion' => '10 o más años',
            'activo' => true,
        ]);
    }
}