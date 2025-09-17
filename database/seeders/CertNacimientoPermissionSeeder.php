<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class CertNacimientoPermissionSeeder extends Seeder
{
    public function run(): void
    {
        // Resetear caché de permisos
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Lista de permisos específicos para CERTIFICADO DE NACIMIENTO
        $permissions = [
            'ver certificados nacimiento',
            'crear certificados nacimiento',
            'editar certificados nacimiento',
            'eliminar certificados nacimiento',
            'descargar pdf certificados nacimiento',
            'gestionar certificados nacimiento',
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
            'ver certificados nacimiento',
            'crear certificados nacimiento',
            'editar certificados nacimiento',
            'eliminar certificados nacimiento',
            'descargar pdf certificados nacimiento',
            'gestionar certificados nacimiento',
        ]);

        // Permisos para técnico (solo ver y descargar)
        $tecnico->givePermissionTo([
            'ver certificados nacimiento',
            'descargar pdf certificados nacimiento',
        ]);

        // Permisos para archivo (gestión de archivos)
        $archivos->givePermissionTo([
            'ver certificados nacimiento',
            'gestionar certificados nacimiento',
            'descargar pdf certificados nacimiento',
        ]);

        $this->command->info('✅ Permisos de CERTIFICADO DE NACIMIENTO creados y asignados correctamente a los roles.');
    }
}
