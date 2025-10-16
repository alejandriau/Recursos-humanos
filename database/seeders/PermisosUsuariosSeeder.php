<?php
// database/seeders/PermisosUsuariosSeeder.php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PermisosUsuariosSeeder extends Seeder
{
    public function run()
    {
        // Permisos para el módulo de Gestión de Usuarios
        $permisos = [
            // Gestión de Usuarios
            'ver_usuarios',
            'crear_usuarios',
            'editar_usuarios',
            'eliminar_usuarios',

            // Gestión de Roles
            'ver_roles',
            'crear_roles',
            'editar_roles',
            'eliminar_roles',

            // Gestión de Permisos de Usuarios
            'asignar_roles_usuarios',
            'asignar_permisos_directos_usuarios',

            // Gestión de Permisos de Roles
            'gestionar_permisos_roles'
        ];

        foreach ($permisos as $permiso) {
            Permission::firstOrCreate(['name' => $permiso, 'guard_name' => 'web']);
        }

        // Asignar permisos a roles
        $admin = Role::where('name', 'admin')->first();
        $jefeUnidad = Role::where('name', 'Jefe_unidad')->first();
        $tecnico = Role::where('name', 'tecnico')->first();
        $archivo = Role::where('name', 'archivo')->first();

        // Admin: Todos los permisos
        if ($admin) {
            $admin->givePermissionTo($permisos);
        }

        // Jefe de Unidad: Puede ver usuarios pero no gestionar roles/permisos completos
        if ($jefeUnidad) {
            $jefeUnidad->givePermissionTo([
                'ver_usuarios',
                'ver_roles'
            ]);
        }

        // Técnico y Archivo: Solo lectura básica
        if ($tecnico) {
            $tecnico->givePermissionTo([
                'ver_usuarios'
            ]);
        }

        if ($archivo) {
            $archivo->givePermissionTo([
                'ver_usuarios'
            ]);
        }
    }
}
