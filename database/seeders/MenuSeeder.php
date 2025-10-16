<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class MenuSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = [
            'ver inicio',
            'ver personal',
            'ver perfiles',
            'ver puestos',
            'ver pasivos',
            'ver perfil profesional',
            'ver biblioteca planillas',
            'ver asignar item',
            'ver altas bajas',
            'ver bajas',
            'ver configuracion',
            'ver organizacional',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
        }

        $admin = Role::firstOrCreate(['name' => 'admin']);
        $jefeUnidad = Role::firstOrCreate(['name' => 'jefe_unidad']);
        $tecnico = Role::firstOrCreate(['name' => 'tecnico']);
        $archivo = Role::firstOrCreate(['name' => 'archivo']);

        $admin->syncPermissions(Permission::all());

        $jefeUnidad->syncPermissions([
            'ver inicio', 'ver personal',
            'ver perfiles', 'ver puestos', 'ver perfil profesional', 'ver configuracion', 'ver organizacional',
        ]);

        $tecnico->syncPermissions([
            'ver inicio', 'ver personal', 'ver perfiles', 'ver puestos',
            'ver perfil profesional', 'ver biblioteca planillas', 'ver asignar item', 'ver organizacional',
        ]);

        $archivo->syncPermissions([
            'ver inicio', 'ver biblioteca planillas',
        ]);
    }
}
