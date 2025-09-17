<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class CroquiPermissionSeeder extends Seeder
{
    public function run(): void
    {
        // Resetear caché de permisos
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Lista de permisos específicos para CROQUIS
        $permissions = [
            'ver croquis',
            'crear croquis',
            'editar croquis',
            'eliminar croquis',
            'gestionar croquis',
            'ver mapa croquis',
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

        // Asignar permisos al administrador (todos los permisos de CROQUIS)
        $admin->givePermissionTo($permissions);

        // Permisos para jefe de unidad (gestión completa de CROQUIS)
        $jefeUnidad->givePermissionTo([
            'ver croquis',
            'crear croquis',
            'editar croquis',
            'eliminar croquis',
            'gestionar croquis',
            'ver mapa croquis',
        ]);

        // Permisos para técnico (solo ver y ver mapa)
        $tecnico->givePermissionTo([
            'ver croquis',
            'ver mapa croquis',
        ]);

        // Permisos para archivo (gestión)
        $archivos->givePermissionTo([
            'ver croquis',
            'gestionar croquis',
            'ver mapa croquis',
        ]);

        $this->command->info('✅ Permisos de CROQUIS creados y asignados correctamente a los roles.');
    }
}
