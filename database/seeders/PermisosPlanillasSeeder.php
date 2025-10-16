<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;

class PermisosPlanillasSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cache
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Crear SOLO permisos para planillas PDF
        $permisosPlanillas = [
            'ver_planillas',
            'crear_planillas',
            'editar_planillas',
            'eliminar_planillas',
            'descargar_planillas',
            'gestionar_planillas',
        ];

        foreach ($permisosPlanillas as $permiso) {
            Permission::firstOrCreate(['name' => $permiso]);
        }

        // Obtener roles existentes
        $admin = Role::firstOrCreate(['name' => 'admin']);
        $jefeUnidad = Role::firstOrCreate(['name' => 'jefe_unidad']);
        $tecnico = Role::firstOrCreate(['name' => 'tecnico']);
        $archivos = Role::firstOrCreate(['name' => 'archivo']);

        // Asignar permisos de planillas a roles
        $admin->givePermissionTo($permisosPlanillas);

        $jefeUnidad->givePermissionTo([
            'ver_planillas',
            'descargar_planillas',
        ]);

        $archivos->givePermissionTo([
            'ver_planillas',
            'crear_planillas',
            'editar_planillas',
            'eliminar_planillas',
            'descargar_planillas',
            'gestionar_planillas',
        ]);

        $tecnico->givePermissionTo([
            'ver_planillas',
            'descargar_planillas',
        ]);
    }
}
