<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Yajra\DataTables\Facades\DataTables;

class RoleController extends Controller
{
    public function index()
    {
        // Mengambil data roles dengan permissions terkait
        $roles = Role::with('permissions:name')->get()->map(function ($role) {
            return [
                'id' => $role->id,
                'name' => $role->name,
                'permissions' => $role->permissions->pluck('name')->toArray()
            ];
        });

        // Mengambil data users dengan roles terkait
        $users = User::with('roles:name')->get()->map(function ($user) {
            return [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'roles' => $user->roles->pluck('name')->toArray()
            ];
        });

        // Mengambil data permissions
        $permissions = Permission::all(['id', 'name'])->map(function ($permission) {
            return [
                'id' => $permission->id,
                'name' => $permission->name,
            ];
        });

        // Menyusun data dalam format DataTables untuk `roles`
        return DataTables::of($roles)
            ->with([
                'users' => $users,     
                'permissions' => $permissions  
            ])
            ->toJson();
    }

    public function indexAssignRole()
    {
        // Mengambil data users dengan roles terkait
        $users = User::with('roles:name')->get()->map(function ($user) {
            return [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'roles' => $user->roles->pluck('name')->toArray()
            ];
        });

        return DataTables::of($users)->toJson();
    }

    public function create()
    {
        $roles = Role::all();
        $permissions = Permission::all();

        // Mengelompokkan permissions berdasarkan modul
        $groupedPermissions = $permissions->groupBy(function ($permission) {
            return explode('.', $permission->name)[0]; // Menggunakan bagian pertama dari nama permission sebagai modul
        });

        return response()->json([
            'roles' => $roles,
            'permissions' => $groupedPermissions,
        ]);
    }

    public function store(Request $request)
    {
        // $this->authorize('create', Role::class);

        $role = Role::create(['name' => $request->name]);
        $role->syncPermissions($request->permissions);

        return response()->json(['message' => 'Role created successfully', 'role' => $role]);
    }

    public function edit($id)
    {
        $role = Role::with('permissions')->findOrFail($id);
        $permissions = Permission::all();

        // Mengubah permissions role menjadi array nama permission
        $rolePermissions = $role->permissions->pluck('name')->toArray();

        // Mengelompokkan permissions berdasarkan modul
        $groupedPermissions = $permissions->groupBy(function ($permission) {
            return explode('.', $permission->name)[0]; // Bagian pertama dari nama sebagai modul
        });

        return response()->json([
            'role' => $role,
            'rolePermissions' => $rolePermissions, // Mengirimkan array nama permissions
            'groupedPermissions' => $groupedPermissions,
        ]);
    }

    public function update(Request $request, Role $role)
    {
        // $this->authorize('edit', $role);

        $role->update(['name' => $request->name]);
        $role->syncPermissions($request->permissions);

        return response()->json(['status' => 'success', 'message' => 'Role updated successfully', 'role' => $role]);    
    }

    public function destroy(Role $role)
    {
        // $this->authorize('destroy', $role);

        $role->delete();
        return response()->json(['message' => 'Role deleted successfully']);
    }

    public function storeUser(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
            'role' => 'required|string|exists:roles,name',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => bcrypt($validated['password']),
        ]);

        $user->syncRoles([$validated['role']]);

        return response()->json([
            'status' => 'success',
            'message' => 'User created successfully',
            'user' => $user->load('roles'),
        ], 201);
    }
    
    public function assignRole(Request $request, User $user)
    {
        $request->validate([
            'roles' => 'array|required',
            'roles.*' => 'exists:roles,name',
        ]);

        $user->syncRoles($request->roles);

        return response()->json(['status' => 'success', 'message' => 'User roles updated successfully', 'user' => $user->load('roles')], 200);    
    }

    public function userDestroy($id)
    {
        $user = User::find($id);

        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        $user->delete(); // Menghapus user berdasarkan ID
        return response()->json(['message' => 'User deleted successfully']);
    }

}
