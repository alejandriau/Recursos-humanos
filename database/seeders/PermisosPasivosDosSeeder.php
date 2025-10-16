<?php
// database/seeders/PermisosPasivosDosSeeder.php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PermisosPasivosDosSeeder extends Seeder
{
    public function run()
    {
        // Permisos para el módulo de Pasivos Dos
        $permisos = [
            // Visualización
            'ver_pasivos_dos',
            'ver_ultimo_registro_pasivos_dos',

            // Filtros y búsquedas
            'filtrar_letra_pasivos_dos',
            'buscar_pasivos_dos',

            // Operaciones CRUD
            'crear_pasivos_dos',
            'editar_pasivos_dos',
            'eliminar_pasivos_dos',

            // Operaciones específicas
            'seleccionar_pasivos_dos',
            'generar_pdf_pasivos_dos',

            // Gestión de selecciones
            'eliminar_seleccion_pasivos_dos'
        ];

        foreach ($permisos as $permiso) {
            Permission::firstOrCreate(['name' => $permiso, 'guard_name' => 'web']);
        }

        // Asignar permisos a roles
        $admin = Role::where('name', 'admin')->first();
        $jefeUnidad = Role::where('name', 'Jefe_unidad')->first();
        $tecnico = Role::where('name', 'tecnico')->first();
        $archivo = Role::where('name', 'archivo')->first();

        if ($admin) {
            $admin->givePermissionTo($permisos); // Todos los permisos
        }

        if ($jefeUnidad) {
            $jefeUnidad->givePermissionTo([
                'ver_pasivos_dos',
                'ver_ultimo_registro_pasivos_dos',
                'filtrar_letra_pasivos_dos',
                'buscar_pasivos_dos',
                'crear_pasivos_dos',
                'editar_pasivos_dos',
                'seleccionar_pasivos_dos',
                'generar_pdf_pasivos_dos',
                'eliminar_seleccion_pasivos_dos'
            ]);
        }

        if ($tecnico) {
            $tecnico->givePermissionTo([
                'ver_pasivos_dos',
                'ver_ultimo_registro_pasivos_dos',
                'filtrar_letra_pasivos_dos',
                'buscar_pasivos_dos',
                'seleccionar_pasivos_dos',
                'generar_pdf_pasivos_dos',
                'eliminar_seleccion_pasivos_dos'
            ]);
        }

        if ($archivo) {
            $archivo->givePermissionTo([
                'ver_pasivos_dos',
                'filtrar_letra_pasivos_dos',
                'buscar_pasivos_dos',
                'editar_pasivos_dos',
                'seleccionar_pasivos_dos',
                'generar_pdf_pasivos_dos',
                'eliminar_seleccion_pasivos_dos'
            ]);
        }
    }
}
