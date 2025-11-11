<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->withPersonalTeam()->create();

        $this->call([
            //RolesYPermisosSeeder::class,
            AfpPermissionSeeder::class,
            CajaCordePermissionSeeder::class,
            CedulaIdentidadPermissionSeeder::class,
            CenviPermissionSeeder::class,
            CertNacimientoPermissionSeeder::class,
            CompromisoPermissionSeeder::class,
            CroquiPermissionSeeder::class,
            CurriculumPermissionSeeder::class,
            DjbrentaPermissionSeeder::class,
            ForconsanguiPermissionSeeder::class,
            Formulario1PermissionSeeder::class,
            Formulario2PermissionSeeder::class,
            LicenciaConducirPermissionSeeder::class,
            LicenciaMilitarPermissionSeeder::class,
            MenuSeeder::class,
            PermisosPasivosDosSeeder::class,
            PermisosPlanillasSeeder::class,
            PermisosUsuariosSeeder::class,
            PermissionSeeder::class,
        ]);
    }
}
