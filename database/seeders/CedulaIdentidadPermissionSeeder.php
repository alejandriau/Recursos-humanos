<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class CedulaIdentidadPermissionSeeder extends Seeder
{
    public function run(): void
    {
        // Resetear caché de permisos
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Lista de permisos específicos para CÉDULA DE IDENTIDAD
        $permissions = [
            'ver cedulas',
            'crear cedulas',
            'editar cedulas',
            'eliminar cedulas',
            'descargar pdf cedulas',
            'gestionar cedulas',
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
            'ver cedulas',
            'crear cedulas',
            'editar cedulas',
            'eliminar cedulas',
            'descargar pdf cedulas',
            'gestionar cedulas',
        ]);

        // Permisos para técnico (solo ver y descargar)
        $tecnico->givePermissionTo([
            'ver cedulas',
            'descargar pdf cedulas',
        ]);

        // Permisos para archivo (gestión de archivos)
        $archivos->givePermissionTo([
            'ver cedulas',
            'gestionar cedulas',
            'descargar pdf cedulas',
        ]);

        $this->command->info('✅ Permisos de CÉDULA DE IDENTIDAD creados y asignados correctamente a los roles.');
    }
}
