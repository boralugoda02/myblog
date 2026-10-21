<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        
        Schema::create('roles', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('slug')->unique();          // saved in users.role
            $table->string('description')->nullable();
            $table->boolean('is_system')->default(false);
            $table->timestamps();
        });

        
        Schema::create('role_has_permissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('role_id')->constrained('roles')->cascadeOnDelete();
            $table->foreignId('permission_id')->constrained('permissions')->cascadeOnDelete();
            $table->unique(['role_id', 'permission_id']);
        });

        
        $now = now();
        DB::table('roles')->insert([
            ['name' => 'Admin',  'slug' => 'admin',  'description' => 'Full access to everything. This role cannot be changed.', 'is_system' => true, 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Author', 'slug' => 'author', 'description' => 'Can write posts and send them for approval.',              'is_system' => true, 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'User',   'slug' => 'user',   'description' => 'Default role for registered users.',                       'is_system' => true, 'created_at' => $now, 'updated_at' => $now],
        ]);

        
        Schema::table('users', function (Blueprint $table) {
            $table->string('role')->default('user')->change();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('role_has_permissions');
        Schema::dropIfExists('roles');
    }
};