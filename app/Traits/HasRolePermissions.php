<?php

namespace App\Traits;

use App\Models\Role;

trait HasRolePermissions
{
    protected ?array $rolePermissionSlugs = null;

    
    public function hasRolePermission(string $slug): bool
    {
        if ($this->role === 'admin') {
            return true; // admin always has everything
        }

        if ($this->rolePermissionSlugs === null) {
            $role = Role::where('slug', $this->role)->first();
            $this->rolePermissionSlugs = $role ? $role->permissions()->pluck('slug')->all() : [];
        }

        return in_array($slug, $this->rolePermissionSlugs, true);
    }
}