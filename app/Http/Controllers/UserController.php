<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\ChecksPermissions;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    use ChecksPermissions;
    public function index()
    {
        $this->requirePermission('users.view');

        $users = User::with('role')->latest()->paginate(20);
        return view('users.index', compact('users'));
    }

    public function create()
    {
        $this->requirePermission('users.create');

        $roles = Role::all();
        return view('users.create', compact('roles'));
    }

    public function store(Request $request)
    {
        $this->requirePermission('users.create');

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'role_id' => ['required', 'exists:roles,id'],
        ]);

        $validated['password'] = Hash::make($validated['password']);

        User::create($validated);

        return redirect()->route('users.index')
            ->with('success', 'User created successfully!');
    }

    public function show(User $user)
    {
        $this->requirePermission('users.view');

        $user->load('role');
        return view('users.show', compact('user'));
    }

    public function edit(User $user)
    {
        $this->requirePermission('users.edit');

        $roles = Role::all();
        return view('users.edit', compact('user', 'roles'));
    }

    public function update(Request $request, User $user)
    {
        $this->requirePermission('users.edit');

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email,' . $user->id],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
            'role_id' => ['required', 'exists:roles,id'],
        ]);

        if (isset($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        } else {
            unset($validated['password']);
        }

        $user->update($validated);

        return redirect()->route('users.index')
            ->with('success', 'User updated successfully!');
    }

    public function destroy(User $user)
    {
        $this->requirePermission('users.delete');

        if ($user->isSuperAdmin() && User::whereHas('role', function($q) {
            $q->where('slug', 'super-admin');
        })->count() === 1) {
            return redirect()->route('users.index')
                ->with('error', 'Cannot delete the last Super Admin!');
        }

        $user->delete();
        return redirect()->route('users.index')
            ->with('success', 'User deleted successfully!');
    }
}
