<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Permission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class PermissionController extends Controller
{
    public function index()
{
    $permissions = Permission::orderBy('group')->orderBy('name')->get()->groupBy('group');
    $roles       = ['admin', 'author', 'user'];

    $rolePermissions = [];
    foreach ($roles as $role) {
        $rolePermissions[$role] = DB::table('role_permission')
            ->where('role', $role)
            ->pluck('permission_id')
            ->map(fn ($id) => (int) $id)
            ->toArray();
    }

    $roleCounts = [];
    foreach ($roles as $role) {
        $roleCounts[$role] = \App\Models\User::where('role', $role)->count();
    }

    return view('admin.permissions', compact('permissions', 'roles', 'rolePermissions', 'roleCounts'));
}

    public function update(Request $request)
    {
        $roles = ['admin', 'author', 'user'];

        foreach ($roles as $role) {
            if ($role === 'admin') continue; 

            $permIds = $request->input("permissions.$role", []);

            DB::table('role_permission')->where('role', $role)->delete();

            foreach ($permIds as $permId) {
                DB::table('role_permission')->insert([
                    'role'          => $role,
                    'permission_id' => $permId,
                ]);
            }
        }

        return back()->with('success', 'Permissions updated successfully!');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'  => 'required|string|max:100',
            'slug'  => 'required|string|unique:permissions,slug',
            'group' => 'required|string|max:50',
        ]);

        Permission::create($request->only('name', 'slug', 'group'));

        return back()->with('success', 'Permission added successfully!');
    }

    public function destroy(Permission $permission)
    {
        DB::table('role_permission')->where('permission_id', $permission->id)->delete();
        $permission->delete();

        return back()->with('success', 'Permission deleted!');
    }
    public function updateDetails(Request $request, Permission $permission)
{
    
    $protected = ['posts.create', 'posts.edit', 'posts.delete', 'admin.access'];

    $rules = [
        'name'  => 'required|string|max:255',
        'group' => 'required|string|max:100',
    ];

    if (! in_array($permission->slug, $protected)) {
        $rules['slug'] = [
            'required', 'string', 'max:255',
            'regex:/^[a-z0-9]+(?:[._-][a-z0-9]+)*$/',
            Rule::unique($permission->getTable(), 'slug')->ignore($permission->id),
        ];
    }

    $permission->update($request->validate($rules));

    return back()->with('success', 'Permission updated successfully.');
}
}