<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Http\Request;
use App\Services\ActivityLogService;

class RoleController extends Controller
{
    public function index()
    {
        $roles = Role::with('permissions')->get();
        return view('admin.roles.index', compact('roles'));
    }

    public function create()
    {
        $permissions = Permission::all()->groupBy(fn($p) => explode('.', $p->name)[0]);
        return view('admin.roles.create', compact('permissions'));
    }

    public function store(Request $request)
    {
        $request->validate(['name' => 'required|string|unique:roles,name']);
        $role = Role::create(['name' => $request->name, 'guard_name' => 'web']);

        if ($request->permissions) {
            $role->syncPermissions($request->permissions);
        }

        ActivityLogService::created('Role', $role->id, "Created role \"{$role->name}\"");
        return redirect()->route('admin.roles.index')->with('success', 'Role created.');
    }

    public function edit(Role $role)
    {
        $permissions = Permission::all()->groupBy(fn($p) => explode('.', $p->name)[0]);
        $rolePermissions = $role->permissions->pluck('name')->toArray();
        return view('admin.roles.edit', compact('role', 'permissions', 'rolePermissions'));
    }

    public function update(Request $request, Role $role)
    {
        $request->validate(['name' => 'required|string|unique:roles,name,' . $role->id]);
        $role->update(['name' => $request->name]);

        if ($request->permissions) {
            $role->syncPermissions($request->permissions);
        } else {
            $role->syncPermissions([]);
        }

        ActivityLogService::updated('Role', $role->id, "Updated role \"{$role->name}\"");
        return redirect()->route('admin.roles.index')->with('success', 'Role updated.');
    }

    public function destroy(Role $role)
    {
        ActivityLogService::deleted('Role', $role->id, "Deleted role \"{$role->name}\"");
        $role->delete();
        return back()->with('success', 'Role deleted.');
    }

    public function show(Role $role) { return redirect()->route('admin.roles.edit', $role); }

    public function updatePermissions(Request $request, Role $role)
    {
        $role->syncPermissions($request->permissions ?? []);
        ActivityLogService::updated('Role', $role->id, "Updated permissions for role \"{$role->name}\"");
        return back()->with('success', 'Permissions updated.');
    }
}
