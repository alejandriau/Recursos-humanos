<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class DjbrentaPermissionSeeder extends Seeder
{
    public function run(): void
    {
        // Resetear caché de permisos
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Lista de permisos específicos para DJBRENTA
        $permissions = [
            'ver djbrentas',
            'crear djbrentas',
            'editar djbrentas',
            'eliminar djbrentas',
            'descargar pdf djbrentas',
            'gestionar djbrentas',
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

        // Asignar permisos al administrador (todos los permisos de DJBRENTA)
        $admin->givePermissionTo($permissions);

        // Permisos para jefe de unidad (gestión completa de DJBRENTA)
        $jefeUnidad->givePermissionTo([
            'ver djbrentas',
            'crear djbrentas',
            'editar djbrentas',
            'eliminar djbrentas',
            'descargar pdf djbrentas',
            'gestionar djbrentas',
        ]);

        // Permisos para técnico (solo ver y descargar)
        $tecnico->givePermissionTo([
            'ver djbrentas',
            'descargar pdf djbrentas',
        ]);

        // Permisos para archivo (gestión de archivos)
        $archivos->givePermissionTo([
            'ver djbrentas',
            'gestionar djbrentas',
            'descargar pdf djbrentas',
        ]);

        $this->command->info('✅ Permisos de DJBRENTA creados y asignados correctamente a los roles.');
    }
}
