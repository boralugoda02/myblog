<?php

namespace Database\Seeders;

use App\Models\Permission;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PermissionSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = [
            
            ['name' => 'Write and submit posts',   'slug' => 'posts.create',  'group' => 'Posts'],
            ['name' => 'Edit own posts',            'slug' => 'posts.edit',    'group' => 'Posts'],
            ['name' => 'Delete own posts',          'slug' => 'posts.delete',  'group' => 'Posts'],
            ['name' => "See every author's posts",  'slug' => 'posts.viewAll', 'group' => 'Posts'],
            ['name' => 'Approve / reject posts',    'slug' => 'posts.approve', 'group' => 'Posts'],

            
            ['name' => 'View all users',            'slug' => 'users.view',    'group' => 'Users'],
            ['name' => 'Change user roles',         'slug' => 'users.role',    'group' => 'Users'],
            ['name' => 'Delete users',              'slug' => 'users.delete',  'group' => 'Users'],
            ['name' => 'Ban / unban users',         'slug' => 'users.ban',     'group' => 'Users'],

            
            ['name' => 'Access admin dashboard',    'slug' => 'admin.access',  'group' => 'Settings'],
            ['name' => 'Manage SMTP settings',      'slug' => 'smtp.manage',   'group' => 'Settings'],
            ['name' => 'Manage categories',         'slug' => 'categories.manage', 'group' => 'Settings'],
        ];

        foreach ($permissions as $perm) {
            Permission::firstOrCreate(['slug' => $perm['slug']], $perm);
        }

        
        $adminPerms = Permission::all()->pluck('id');
        $userPerms  = Permission::whereIn('slug', [
            'posts.create', 'posts.edit', 'posts.delete'
        ])->pluck('id');
        $authorPerms = Permission::whereIn('slug', [
            'posts.create', 'posts.edit', 'posts.delete', 'posts.viewAll'
        ])->pluck('id');

        
        foreach ($adminPerms as $id) {
            DB::table('role_permission')->insertOrIgnore([
                'role' => 'admin', 'permission_id' => $id
            ]);
        }

        
        foreach ($authorPerms as $id) {
            DB::table('role_permission')->insertOrIgnore([
                'role' => 'author', 'permission_id' => $id
            ]);
        }

        
        foreach ($userPerms as $id) {
            DB::table('role_permission')->insertOrIgnore([
                'role' => 'user', 'permission_id' => $id
            ]);
        }
    }
}