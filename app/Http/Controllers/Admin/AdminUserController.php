<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class AdminUserController extends Controller
{
    public function index()
    {
        $adminRoles = ['Super Admin', 'Admin', 'Manager', 'Sales Executive', 'Inventory Manager', 'Order Manager', 'Customer Support'];
        $users = User::role($adminRoles)->with('roles')->paginate(20);
        return view('admin.admin-users.index', compact('users'));
    }

    public function create()
    {
        $roles = Role::whereNotIn('name', ['Customer'])->get();
        return view('admin.admin-users.create', compact('roles'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email',
            'phone'    => 'nullable|string|max:20',
            'password' => ['required', Password::defaults()],
            'role'     => 'required|exists:roles,name',
        ]);

        $user = User::create([
            'name'     => $data['name'],
            'email'    => $data['email'],
            'phone'    => $data['phone'] ?? null,
            'password' => Hash::make($data['password']),
            'status'   => 'active',
        ]);

        $user->assignRole($data['role']);

        return redirect()->route('admin.admin-users.index')->with('success', 'Admin user created.');
    }

    public function edit(User $adminUser)
    {
        $roles = Role::whereNotIn('name', ['Customer'])->get();
        return view('admin.admin-users.edit', compact('adminUser', 'roles'));
    }

    public function update(Request $request, User $adminUser)
    {
        $data = $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email,' . $adminUser->id,
            'phone'    => 'nullable|string|max:20',
            'password' => ['nullable', Password::defaults()],
        ]);

        $updateData = ['name' => $data['name'], 'email' => $data['email'], 'phone' => $data['phone'] ?? null];
        if (!empty($data['password'])) {
            $updateData['password'] = Hash::make($data['password']);
        }

        $adminUser->update($updateData);

        return redirect()->route('admin.admin-users.index')->with('success', 'Admin user updated.');
    }

    public function destroy(User $adminUser)
    {
        if ($adminUser->id === auth()->id()) {
            return back()->withErrors(['error' => 'Cannot delete yourself.']);
        }
        $adminUser->delete();
        return back()->with('success', 'Admin user deleted.');
    }

    public function show(User $adminUser) { return redirect()->route('admin.admin-users.edit', $adminUser); }

    public function assignRole(Request $request, User $user)
    {
        $request->validate(['role' => 'required|exists:roles,name']);
        $user->syncRoles([$request->role]);
        return back()->with('success', 'Role assigned.');
    }
}
