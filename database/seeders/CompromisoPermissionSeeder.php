<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class CompromisoPermissionSeeder extends Seeder
{
    public function run(): void
    {
        // Resetear caché de permisos
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Lista de permisos específicos para COMPROMISO
        $permissions = [
            'ver compromisos',
            'crear compromisos',
            'editar compromisos',
            'eliminar compromisos',
            'descargar pdf compromisos',
            'gestionar compromisos',
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

        // Asignar permisos al administrador (todos los permisos de COMPROMISO)
        $admin->givePermissionTo($permissions);

        // Permisos para jefe de unidad (gestión completa de COMPROMISO)
        $jefeUnidad->givePermissionTo([
            'ver compromisos',
            'crear compromisos',
            'editar compromisos',
            'eliminar compromisos',
            'descargar pdf compromisos',
            'gestionar compromisos',
        ]);

        // Permisos para técnico (solo ver y descargar)
        $tecnico->givePermissionTo([
            'ver compromisos',
            'descargar pdf compromisos',
        ]);

        // Permisos para archivo (gestión de archivos)
        $archivos->givePermissionTo([
            'ver compromisos',
            'gestionar compromisos',
            'descargar pdf compromisos',
        ]);

        $this->command->info('✅ Permisos de COMPROMISO creados y asignados correctamente a los roles.');
    }
}
