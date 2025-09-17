<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class PermissionSeeder extends Seeder
{
    public function run(): void
    {
        // Resetear caché de permisos
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Lista de permisos
        $permissions = [
            'ver usuarios',
            'crear usuarios',
            'editar usuarios',
            'eliminar usuarios',
            'asignar roles a usuarios',
            'ver reportes',
            'ver certificados',
            'descargar documentos',
            'gestionar archivos',
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
        $admin->syncPermissions(Permission::all());

        // Permisos para jefe de unidad
        $jefeUnidad->syncPermissions([
            'ver usuarios',
            'crear usuarios',
            'editar usuarios',
            'eliminar usuarios',
            'asignar roles a usuarios',
            'ver reportes',
            'ver certificados'
        ]);

        // Permisos para técnico
        $tecnico->syncPermissions([
            'ver usuarios',
            'ver certificados',
            'descargar documentos'
        ]);

        // Permisos para archivo
        $archivos->syncPermissions([
            'gestionar archivos',
            'descargar documentos'
        ]);

        $this->command->info('✅ Permisos creados y asignados correctamente a los roles.');
    }
}
