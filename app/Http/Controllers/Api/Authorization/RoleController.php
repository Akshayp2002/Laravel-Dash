<?php

namespace App\Http\Controllers\Api\Authorization;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Permission;
use App\Models\Role;

class RoleController extends Controller
{

    public function create()
    {
        $data = Permission::fetchAllStaticPermissions();
        return \response()->json($data);
    }

    public function store(Request $request)
    {
        $request->validate([
            'role_name'   => 'required|string|unique:roles,name',
            'permissions' => 'required|array',
        ]);
        // Create Role
        $role = Role::create([
            'name' => ucfirst($request->role_name),
        ]);
        // Attach Permissions
        $role->syncPermissions($request->permissions);
        return response()->json(['message' => 'Role created successfully'], 201);
    }

    public function edit($roleId)
    {
        $role = Role::with('permissions')->findOrFail($roleId);

        return response()->json([
            'role' => [
                'id' => $role->id,
                'name' => $role->name,
                'permissions' => $role->permissions->pluck('name')->values(), // Get assigned permissions
            ],
            'all_permissions' => Permission::pluck('name', 'id'), // Get all available permissions
        ], 200);
    }

    public function update(Request $request, $roleId)
    {
        $request->validate([
            'role_name'     => 'required|string|unique:roles,name,' . $roleId,
            'permissions'   => 'required|array',
            'permissions.*' => 'string|exists:permissions,name',
        ]);

        // Find Role
        $role = Role::findOrFail($roleId);

        // Update Role Name
        $role->update(['name' => ucfirst($request->role_name)]);

        // Sync Permissions
        $role->syncPermissions($request->permissions);

        return response()->json([
            'message' => 'Role updated successfully',
        ], 200);
    }
}
