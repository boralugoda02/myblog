<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class RoleController extends Controller
{
    
    public function index()
    {
        $roles = Role::withCount('permissions')->orderBy('id')->get();
        $userCounts = User::selectRaw('role, COUNT(*) as total')->groupBy('role')->pluck('total', 'role');
        $totalPermissions = Permission::count();

        return view('admin.roles.index', compact('roles', 'userCounts', 'totalPermissions'));
    }

    
    public function create()
    {
        return view('admin.roles.create', [
            'groups'   => $this->groupedPermissions(),
            'selected' => old('permissions', []),
        ]);
    }

    
    public function store(Request $request)
    {
        $data = $this->validated($request);

        $role = Role::create([
            'name'        => $data['name'],
            'slug'        => $this->uniqueSlug($data['name']),
            'description' => $data['description'] ?? null,
            'is_system'   => false,
        ]);
        $role->permissions()->sync($data['permissions'] ?? []);

        return redirect()->route('admin.roles.index')->with('success', 'Role created successfully.');
    }

    
    public function edit(Role $role)
    {
        return view('admin.roles.edit', [
            'role'     => $role,
            'groups'   => $this->groupedPermissions(),
            'selected' => old('permissions', $role->permissions->pluck('id')->all()),
        ]);
    }

    
    public function update(Request $request, Role $role)
    {
        if ($role->isAdmin()) {
            return back()->with('error', 'The Admin role cannot be changed.');
        }

        $data = $this->validated($request, $role);

        $role->update([
            'name'        => $data['name'],
            'description' => $data['description'] ?? null,
        ]);
        $role->permissions()->sync($data['permissions'] ?? []);

        return redirect()->route('admin.roles.index')->with('success', 'Role updated successfully.');
    }

    
    public function destroy(Role $role)
    {
        if ($role->is_system) {
            return back()->with('error', 'System roles cannot be deleted.');
        }

        $count = User::where('role', $role->slug)->count();
        if ($count > 0) {
            return back()->with('error', "This role is used by {$count} user(s). Change their role first.");
        }

        $role->delete();

        return redirect()->route('admin.roles.index')->with('success', 'Role deleted successfully.');
    }

    private function validated(Request $request, ?Role $role = null): array
    {
        return $request->validate([
            'name'          => ['required', 'string', 'max:60', Rule::unique('roles', 'name')->ignore($role?->id)],
            'description'   => ['nullable', 'string', 'max:255'],
            'permissions'   => ['nullable', 'array'],
            'permissions.*' => ['integer', Rule::exists('permissions', 'id')],
        ]);
    }

    private function groupedPermissions()
    {
        return Permission::orderBy('group')->orderBy('name')->get()->groupBy('group');
    }

    private function uniqueSlug(string $name): string
    {
        $base = Str::slug($name) ?: 'role';
        $slug = $base;
        $i = 2;

        while (Role::where('slug', $slug)->exists()) {
            $slug = $base . '-' . $i++;
        }

        return $slug;
    }
}