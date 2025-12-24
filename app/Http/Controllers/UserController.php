<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index()
    {
        // Only Super Admin can access user management
        if (!auth()->user()->isSuperAdmin()) {
            abort(403, 'Only Super Admin can access this page');
        }

        $users = User::with('role')->latest()->paginate(20);
        return view('users.index', compact('users'));
    }

    public function create()
    {
        // Only Super Admin can access user management
        if (!auth()->user()->isSuperAdmin()) {
            abort(403, 'Only Super Admin can access this page');
        }

        $roles = Role::all();
        return view('users.create', compact('roles'));
    }

    public function store(Request $request)
    {
        // Only Super Admin can access user management
        if (!auth()->user()->isSuperAdmin()) {
            abort(403, 'Only Super Admin can access this page');
        }

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
        // Only Super Admin can access user management
        if (!auth()->user()->isSuperAdmin()) {
            abort(403, 'Only Super Admin can access this page');
        }

        $user->load('role');
        return view('users.show', compact('user'));
    }

    public function edit(User $user)
    {
        // Only Super Admin can access user management
        if (!auth()->user()->isSuperAdmin()) {
            abort(403, 'Only Super Admin can access this page');
        }

        $roles = Role::all();
        return view('users.edit', compact('user', 'roles'));
    }

    public function update(Request $request, User $user)
    {
        // Only Super Admin can access user management
        if (!auth()->user()->isSuperAdmin()) {
            abort(403, 'Only Super Admin can access this page');
        }

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
        // Only Super Admin can access user management
        if (!auth()->user()->isSuperAdmin()) {
            abort(403, 'Only Super Admin can access this page');
        }

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
