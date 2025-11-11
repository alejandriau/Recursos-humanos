<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;

class RolesYPermisosSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
                // Reset cache
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Crear permisos
        $permisos = [
            'ver personal',
            'crear personal',
            'editar personal',
            'eliminar personal',
            'ver reportes',
            'gestionar archivos',
            'ver certificados',
            'descargar documentos',
        ];

        foreach ($permisos as $permiso) {
            Permission::firstOrCreate(['name' => $permiso]);
        }

        // Crear roles
        $admin = Role::firstOrCreate(['name' => 'admin']);
        $jefeUnidad = Role::firstOrCreate(['name' => 'jefe_unidad']);
        $coordinador = Role::firstOrCreate(['name' => 'coordinador']);
        $tecnico = Role::firstOrCreate(['name' => 'tecnico']);
        $archivos = Role::firstOrCreate(['name' => 'archivo']);
        $empleado = Role::firstOrCreate(['name' => 'empleado']);

        // Asignar permisos a roles
        $admin->syncPermissions(Permission::all());

        $jefeUnidad->syncPermissions([
            'ver personal',
            'ver reportes',
            'ver certificados',
        ]);
        $coordinador->syncPermissions([
            'ver personal',
            'ver reportes',
            'ver certificados',
        ]);

        $archivos->syncPermissions([
            'gestionar archivos',
            'descargar documentos',
        ]);

        $tecnico->syncPermissions([
            'ver certificados',
            'descargar documentos',
        ]);

        $user = User::find(1);
        $user->assignRole('admin');
    }
}
