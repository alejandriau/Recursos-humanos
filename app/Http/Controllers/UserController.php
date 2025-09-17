<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index()
    {
        $users = User::with('roles')->paginate(10);
        return view('users.index', compact('users'));
    }

    public function create()
    {
        $roles = Role::all();
        return view('users.create', compact('roles'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'roles' => 'nullable|array',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        if ($request->has('roles')) {
            $user->syncRoles($request->roles);
        }

        return redirect()->route('users.index')->with('success', 'Usuario creado exitosamente.');
    }

    public function show(User $user)
    {
        $user->load('roles', 'permissions');
        return view('users.show', compact('user'));
    }

    public function edit(User $user)
    {
        $roles = Role::all();
        $user->load('roles');

        return view('users.edit', compact('user', 'roles'));
    }

    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'password' => 'nullable|string|min:8|confirmed',
        ]);

        $data = [
            'name' => $request->name,
            'email' => $request->email,
        ];

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);

        return redirect()->route('users.index')->with('success', 'Usuario actualizado exitosamente.');
    }

    public function destroy(User $user)
    {
        $user->delete();
        return redirect()->route('users.index')->with('success', 'Usuario eliminado exitosamente.');
    }

    // Métodos específicos para gestión de roles
    public function editRoles(User $user)
    {
        $roles = Role::all();
        $user->load('roles');

        return view('users.edit-roles', compact('user', 'roles'));
    }

    public function updateRoles(Request $request, User $user)
    {
        $request->validate([
            'roles' => 'nullable|array',
        ]);

        $user->syncRoles($request->roles);

        return redirect()->route('users.show', $user)->with('success', 'Roles actualizados exitosamente.');
    }
    public function removeRole(User $user, Role $role)
    {
        $user->removeRole($role);
        return redirect()->route('users.roles.edit', $user)->with('success', 'Rol eliminado exitosamente.');
    }
    // Métodos específicos para gestión de permisos directos
    public function editPermissions(User $user)
    {
        $permissions = Permission::all();
        $user->load('permissions');

        return view('users.edit-permissions', compact('user', 'permissions'));
    }

    public function updatePermissions(Request $request, User $user)
    {
        $request->validate([
            'permissions' => 'nullable|array',
        ]);

        $user->syncPermissions($request->permissions);

        return redirect()->route('users.show', $user)->with('success', 'Permisos actualizados exitosamente.');
    }

    // Métodos específicos para gestión de permisos directos




public function removePermission(User $user, Permission $permission)
{
    $user->revokePermissionTo($permission);

    return redirect()->route('users.permissions.edit', $user)->with('success', 'Permiso eliminado exitosamente.');
}
}
