<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;

class UserManagementController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = User::with('roles');

        if ($request->has('search')) {
            $search = $request->get('search');
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($request->has('role') && $request->get('role') != '') {
            $query->role($request->get('role'));
        }

        if ($request->has('status') && $request->get('status') != '') {
            $query->where('is_active', $request->get('status') == 'active');
        }

        $users = $query->paginate(10)->withQueryString();
        $roles = Role::all();

        return view('admin.users.index', compact('users', 'roles'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $roles = Role::all();
        return view('admin.users.create', compact('roles'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'role' => ['required', 'exists:roles,name'],
            'department' => ['nullable', 'string', 'max:255'],
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'department' => $request->department,
            'is_active' => true,
        ]);

        $user->assignRole($request->role);

        return redirect()->route('admin.users.index')->with('success', 'User created successfully.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $user)
    {
        $roles = Role::all();
        return view('admin.users.edit', compact('user', 'roles'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:users,email,'.$user->id],
            'role' => ['required', 'exists:roles,name'],
            'department' => ['nullable', 'string', 'max:255'],
            'password' => ['nullable', 'confirmed', Rules\Password::defaults()],
        ]);

        $user->update([
            'name' => $request->name,
            'email' => $request->email,
            'department' => $request->department,
        ]);

        if ($request->filled('password')) {
            $user->update(['password' => Hash::make($request->password)]);
        }

        $user->syncRoles([$request->role]);

        return redirect()->route('admin.users.index')->with('success', 'User updated successfully.');
    }

    /**
     * Toggle active status of the user instead of deleting.
     */
    public function destroy(User $user)
    {
        $user->is_active = !$user->is_active;
        $user->save();

        $status = $user->is_active ? 'activated' : 'deactivated';
        return redirect()->route('admin.users.index')->with('success', "User {$status} successfully.");
    }
}

