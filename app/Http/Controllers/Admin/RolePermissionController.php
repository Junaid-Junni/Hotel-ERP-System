<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;

class RolePermissionController extends Controller
{
    public function __construct()
    {
        // Only admin can access
        $this->middleware('role:admin');
    }

    // View all users and their roles
    public function index()
    {
        $users = User::with('roles')->get();
        // dd($users);
        return view('admin.roles.index', compact('users'));
    }

    // Show form to assign roles to a user
    public function editUserRoles($id)
    {
        $user = User::findOrFail($id);
        $roles = Role::all();
        return view('admin.roles.edit', compact('user', 'roles'));
    }

    // Update user roles
    public function updateUserRoles(Request $request, $id)
    {
        $user = User::findOrFail($id);
        $user->syncRoles($request->roles ?? []);
        return redirect()->route('admin.roles.index')->with('success', 'Roles updated.');
    }

    // View all roles and permissions
    public function roles()
    {
        $roles = Role::with('permissions')->get();
        $permissions = Permission::all();
        return view('admin.roles.roles', compact('roles', 'permissions'));
    }

    // Assign permissions to role
    public function updateRolePermissions(Request $request, $roleId)
    {
        $role = Role::findOrFail($roleId);
        $role->syncPermissions($request->permissions ?? []);
        return redirect()->route('admin.roles.roles')->with('success', 'Permissions updated.');
    }
}
