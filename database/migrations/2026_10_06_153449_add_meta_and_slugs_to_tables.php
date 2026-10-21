<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Posts table එකට meta columns එකතු කිරීම
        Schema::table('posts', function (Blueprint $table) {
            if (!Schema::hasColumn('posts', 'meta_title')) {
                $table->string('meta_title')->nullable();
                $table->text('meta_description')->nullable();
                $table->string('meta_keywords')->nullable();
                $table->string('meta_image')->nullable();
            }
        });

        // Categories table එකට slug සහ short_description එකතු කිරීම
        Schema::table('categories', function (Blueprint $table) {
            if (!Schema::hasColumn('categories', 'slug')) {
                $table->string('slug')->unique()->nullable();
            }
            if (!Schema::hasColumn('categories', 'short_description')) {
                $table->text('short_description')->nullable();
            }
        });
    }

    public function down(): void
    {
        Schema::table('posts', function (Blueprint $table) {
            $table->dropColumn(['meta_title', 'meta_description', 'meta_keywords', 'meta_image']);
        });

        Schema::table('categories', function (Blueprint $table) {
            $table->dropColumn(['slug', 'short_description']);
        });
    }
};