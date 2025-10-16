<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class Formulario1PermissionSeeder extends Seeder
{
    public function run(): void
    {
        // Resetear caché de permisos
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Lista de permisos específicos para FORMULARIO1
        $permissions = [
            'ver formularios1',
            'crear formularios1',
            'editar formularios1',
            'eliminar formularios1',
            'gestionar formularios1',
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
            'ver formularios1',
            'crear formularios1',
            'editar formularios1',
            'eliminar formularios1',
            'gestionar formularios1',
        ]);

        // Permisos para técnico (solo ver)
        $tecnico->givePermissionTo([
            'ver formularios1',
        ]);

        // Permisos para archivo (gestión)
        $archivos->givePermissionTo([
            'ver formularios1',
            'gestionar formularios1',
        ]);

        $this->command->info('✅ Permisos de FORMULARIO1 creados y asignados correctamente a los roles.');
    }
}
