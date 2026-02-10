<?php

namespace App\Http\Controllers\Concerns;

trait ChecksPermissions
{
    /**
     * Check if user has permission, abort if not
     */
    protected function requirePermission(string $permission)
    {
        $user = auth()->user();
        
        if (!$user || !$user->hasPermission($permission)) {
            abort(403, 'You do not have permission to perform this action.');
        }
    }

    /**
     * Check if user has any of the given permissions
     */
    protected function requireAnyPermission(array $permissions)
    {
        $user = auth()->user();
        
        if (!$user) {
            abort(403, 'You must be logged in to perform this action.');
        }

        foreach ($permissions as $permission) {
            if ($user->hasPermission($permission)) {
                return;
            }
        }

        abort(403, 'You do not have permission to perform this action.');
    }
}
