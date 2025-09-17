<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class CurriculumPermissionSeeder extends Seeder
{
    public function run(): void
    {
        // Resetear caché de permisos
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Lista de permisos específicos para CURRICULUM
        $permissions = [
            'ver curriculums',
            'crear curriculums',
            'editar curriculums',
            'eliminar curriculums',
            'descargar pdf curriculums',
            'gestionar curriculums',
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
            'ver curriculums',
            'crear curriculums',
            'editar curriculums',
            'eliminar curriculums',
            'descargar pdf curriculums',
            'gestionar curriculums',
        ]);

        // Permisos para técnico (solo ver y descargar)
        $tecnico->givePermissionTo([
            'ver curriculums',
            'descargar pdf curriculums',
        ]);

        // Permisos para archivo (gestión de archivos)
        $archivos->givePermissionTo([
            'ver curriculums',
            'gestionar curriculums',
            'descargar pdf curriculums',
        ]);

        $this->command->info('✅ Permisos de CURRICULUM creados y asignados correctamente a los roles.');
    }
}
