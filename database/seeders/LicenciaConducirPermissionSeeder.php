<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class LicenciaConducirPermissionSeeder extends Seeder
{
    public function run(): void
    {
        // Resetear caché de permisos
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Lista de permisos específicos para LICENCIA DE CONDUCIR
        $permissions = [
            'ver licencias conducir',
            'crear licencias conducir',
            'editar licencias conducir',
            'eliminar licencias conducir',
            'descargar pdf licencias conducir',
            'gestionar licencias conducir',
        ];

        // Crear permisos si no existen
        foreach ($permissions as $permission) {
            Permission::firstOrCreate([
                'name' => $permission,
                'guard_name' => 'web'
            ]);
        }

        // Obtener los roles existentes
        $admin      = Role::where('name', 'admin')->first();
        $jefeUnidad = Role::where('name', 'jefe_unidad')->first();
        $tecnico    = Role::where('name', 'tecnico')->first();
        $archivos   = Role::where('name', 'archivo')->first();

        // Verificar que los roles existen
        if (!$admin || !$jefeUnidad || !$tecnico || !$archivos) {
            throw new \Exception('Uno o más roles no existen en la base de datos');
        }

        // Asignar permisos al administrador (todos los permisos)
        $admin->givePermissionTo($permissions);

        // Permisos para jefe de unidad (gestión completa)
        $jefeUnidad->givePermissionTo([
            'ver licencias conducir',
            'crear licencias conducir',
            'editar licencias conducir',
            'eliminar licencias conducir',
            'descargar pdf licencias conducir',
            'gestionar licencias conducir',
        ]);

        // Permisos para técnico (solo ver y descargar)
        $tecnico->givePermissionTo([
            'ver licencias conducir',
            'descargar pdf licencias conducir',
        ]);

        // Permisos para archivo (gestión de archivos)
        $archivos->givePermissionTo([
            'ver licencias conducir',
            'gestionar licencias conducir',
            'descargar pdf licencias conducir',
        ]);

        $this->command->info('✅ Permisos de LICENCIA DE CONDUCIR creados y asignados correctamente a los roles.');
    }
}
