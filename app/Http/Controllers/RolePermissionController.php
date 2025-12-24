<?php

namespace App\Http\Controllers;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Http\Request;

class RolePermissionController extends Controller
{
    public function index()
    {
        // Only Super Admin can manage permissions
        if (!auth()->user()->isSuperAdmin()) {
            abort(403, 'Only Super Admin can manage permissions');
        }

        $roles = Role::with('permissions')->get();
        $permissions = Permission::orderBy('module')->orderBy('name')->get()->groupBy('module');

        return view('role-permissions.index', compact('roles', 'permissions'));
    }

    public function update(Request $request, Role $role)
    {
        // Only Super Admin can manage permissions
        if (!auth()->user()->isSuperAdmin()) {
            abort(403, 'Only Super Admin can manage permissions');
        }

        $request->validate([
            'permissions' => 'nullable|array',
            'permissions.*' => 'exists:permissions,id',
        ]);

        $role->permissions()->sync($request->input('permissions', []));

        return redirect()->route('role-permissions.index')
            ->with('success', 'Permissions updated successfully for ' . $role->name);
    }
}

