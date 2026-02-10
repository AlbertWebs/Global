<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\ChecksPermissions;
use App\Models\Permission;
use App\Models\Role;
use Illuminate\Http\Request;

class RolePermissionController extends Controller
{
    use ChecksPermissions;

    public function index()
    {
        $this->requirePermission('roles.manage');

        $roles = Role::with('permissions')->get();
        $permissions = Permission::orderBy('module')->orderBy('name')->get()->groupBy('module');

        return view('role-permissions.index', compact('roles', 'permissions'));
    }

    public function update(Request $request, Role $role)
    {
        $this->requirePermission('roles.manage');

        $request->validate([
            'permissions' => 'nullable|array',
            'permissions.*' => 'exists:permissions,id',
        ]);

        $role->permissions()->sync($request->input('permissions', []));

        return redirect()->route('role-permissions.index')
            ->with('success', 'Permissions updated successfully for ' . $role->name);
    }
}

