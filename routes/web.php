<?php

use App\Http\Controllers\Admin\ActivityLogController; // <-- NEW
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\SmtpSettingController;
use App\Http\Controllers\Admin\PermissionController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Auth\GoogleController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\EditorUploadController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\SeoController;
use App\Http\Controllers\TagController;
use App\Http\Controllers\Admin\MailSettingsController;
use App\Models\Post;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\ApiTokenController;

Route::get('/', [PostController::class, 'home'])->name('home');


Route::get('/sitemap.xml', [SeoController::class, 'sitemap'])->name('sitemap');
Route::get('/feed.xml',    [SeoController::class, 'feed'])->name('feed');
Route::get('/robots.txt',  [SeoController::class, 'robots'])->name('robots');


Route::get('/tags',            [TagController::class, 'index'])->name('tags.index');
Route::get('/tags/{tag:slug}', [TagController::class, 'show'])->name('tags.show');


Route::get('/auth/google',          [GoogleController::class, 'redirect'])->name('auth.google');
Route::get('/auth/google/callback', [GoogleController::class, 'callback'])->name('auth.google.callback');

Route::get('/blogs', [PostController::class, 'index'])->name('posts.index');
Route::get('/posts', function () {
    return redirect()->route('posts.index');
});


Route::middleware(['auth', 'can:admin-access'])->prefix('admin-panel')->name('admin.')->group(function () {

    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');


    Route::get('/posts/status/{status}', [AdminDashboardController::class, 'posts'])
        ->whereIn('status', ['pending', 'published', 'rejected'])
        ->name('posts.status');
    Route::post('/posts/{post}/approve', [AdminDashboardController::class, 'approve'])->name('posts.approve');
    Route::post('/posts/{post}/reject',  [AdminDashboardController::class, 'reject'])->name('posts.reject');


    Route::get('/users',                   [AdminDashboardController::class, 'users'])->name('users');
    Route::patch('/users/{user}/role',     [AdminDashboardController::class, 'updateRole'])->name('users.role');
    Route::patch('/users/{user}/ban',      [AdminDashboardController::class, 'banUser'])->name('users.ban');
    Route::delete('/users/{user}/delete',  [AdminDashboardController::class, 'deleteUser'])->name('users.delete');

        Route::get('/api-tokens',            [ApiTokenController::class, 'index'])->name('api-tokens.index');
    Route::post('/api-tokens',           [ApiTokenController::class, 'store'])->name('api-tokens.store');
    Route::delete('/api-tokens/{token}', [ApiTokenController::class, 'destroy'])->name('api-tokens.destroy');


    // Activity log <-- NEW
    Route::get('/activity-log', [ActivityLogController::class, 'index'])->name('activity.index');


    // Roles (6 routes)
    Route::get('/roles',                [RoleController::class, 'index'])->name('roles.index');
    Route::get('/roles/create',         [RoleController::class, 'create'])->name('roles.create');
    Route::post('/roles',               [RoleController::class, 'store'])->name('roles.store');
    Route::get('/roles/{role:slug}/edit', [RoleController::class, 'edit'])->name('roles.edit');
    Route::put('/roles/{role:slug}',    [RoleController::class, 'update'])->name('roles.update');
    Route::delete('/roles/{role:slug}', [RoleController::class, 'destroy'])->name('roles.destroy');


    Route::get('/permissions',                    [PermissionController::class, 'index'])->name('permissions.index');
    Route::post('/permissions',                   [PermissionController::class, 'store'])->name('permissions.store');
    Route::post('/permissions/update',            [PermissionController::class, 'update'])->name('permissions.update');
    Route::delete('/permissions/{permission}',    [PermissionController::class, 'destroy'])->name('permissions.destroy');
    Route::put('/permissions/{permission}/details', [PermissionController::class, 'updateDetails'])->name('permissions.details');


    Route::get('/smtp-settings',  [SmtpSettingController::class, 'index'])->name('smtp.index');
    Route::post('/smtp-settings', [SmtpSettingController::class, 'update'])->name('smtp.update');


    Route::get('/settings/mail',         [MailSettingsController::class, 'edit'])->name('mail.edit');
    Route::put('/settings/mail',         [MailSettingsController::class, 'update'])->name('mail.update');
    Route::post('/settings/mail/test',   [MailSettingsController::class, 'test'])->name('mail.test');
});

Route::get('/blogs/{post:slug}', [PostController::class, 'show'])->name('posts.show');


Route::middleware(['auth'])->group(function () {

    Route::get('/dashboard', [PostController::class, 'dashboard'])->name('dashboard');


    Route::get('/profile',    [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile',  [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');


    Route::get('/notifications',              [NotificationController::class, 'index'])->name('notifications.index');
    Route::post('/notifications/read-all',    [NotificationController::class, 'readAll'])->name('notifications.readAll');
    Route::get('/notifications/{id}/open',    [NotificationController::class, 'open'])->name('notifications.open');


    Route::post('/editor/upload-image', [EditorUploadController::class, 'store'])->name('editor.upload');


    Route::get('/posts/trash',                [PostController::class, 'trash'])->name('posts.trash');
    Route::post('/posts/{id}/restore',        [PostController::class, 'restore'])->name('posts.restore');
    Route::delete('/posts/{id}/force-delete', [PostController::class, 'forceDelete'])->name('posts.force-delete');
    Route::get('/posts/create',               [PostController::class, 'create'])->name('posts.create');
    Route::post('/posts',                     [PostController::class, 'store'])->name('posts.store');
    Route::get('/posts/{post}/edit',          [PostController::class, 'edit'])->name('posts.edit');
    Route::put('/posts/{post}',               [PostController::class, 'update'])->name('posts.update');
    Route::delete('/posts/{post}',            [PostController::class, 'destroy'])->name('posts.destroy');
    Route::post('/posts/{post}/submit',       [PostController::class, 'submit'])->name('posts.submit');


    Route::post('/posts/{post}/comments', [CommentController::class, 'store'])->name('comments.store');
    Route::put('/comments/{comment}',     [CommentController::class, 'update'])->name('comments.update');
    Route::delete('/comments/{comment}',  [CommentController::class, 'destroy'])->name('comments.destroy');


    Route::resource('categories', CategoryController::class)
        ->only(['index', 'store', 'update', 'destroy']);
});

Route::get('/posts/{post:slug}', function (Post $post) {
    return redirect()->route('posts.show', $post->slug);
});

require __DIR__.'/auth.php';