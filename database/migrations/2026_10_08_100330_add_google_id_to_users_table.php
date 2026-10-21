<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Add each column only if it is missing (safe to re-run after a half-finished migration)
        Schema::table('users', function (Blueprint $table) {
            if (! Schema::hasColumn('users', 'avatar')) {
                $table->string('avatar')->nullable();
            }
            if (! Schema::hasColumn('users', 'bio')) {
                $table->text('bio')->nullable();
            }
            if (! Schema::hasColumn('users', 'google_id')) {
                $table->string('google_id')->nullable();
            }
        });

        if (! Schema::hasIndex('users', 'users_google_id_unique')) {
            Schema::table('users', function (Blueprint $table) {
                $table->unique('google_id');
            });
        }

        // Google-only users have no password
        Schema::table('users', function (Blueprint $table) {
            $table->string('password')->nullable()->change();
        });
    }

    public function down(): void
    {
        if (Schema::hasIndex('users', 'users_google_id_unique')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropUnique('users_google_id_unique');
            });
        }

        Schema::table('users', function (Blueprint $table) {
            foreach (['avatar', 'bio', 'google_id'] as $column) {
                if (Schema::hasColumn('users', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};