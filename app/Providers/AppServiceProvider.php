<?php

namespace App\Providers;

use App\Models\ActivityLog;
use App\Models\Post;
use App\Models\Setting;
use App\Models\User;
use App\Observers\PostObserver;
use Illuminate\Auth\Events\Login;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Support\Facades\Crypt;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        Gate::define('admin-access', fn (User $user) => $user->isAdmin());
        Gate::define('manage-categories', fn (User $user) => $user->isAdmin());
        Gate::define('manage-users', fn (User $user) => $user->isAdmin());

        Paginator::useBootstrapFive();

        // Activity log: write a line every time someone logs in
        Event::listen(Login::class, function (Login $event) {
            ActivityLog::record(
                'auth.login',
                $event->user->name . ' logged in',
                null,
                $event->user->id
            );
        });

        // Activity log: watch every post change (create, edit, approve, reject, delete...)
        Post::observe(PostObserver::class);

        try {
            if (! Schema::hasTable('settings')) {
                return;
            }

            $mail = Setting::group('mail');

            if (empty($mail['host'])) {
                return;
            }

            $password = null;
            if (! empty($mail['password'])) {
                try {
                    $password = Crypt::decryptString($mail['password']);
                } catch (DecryptException $e) {
                    $password = null;
                }
            }

            config([
                'mail.default'                => 'smtp',
                'mail.mailers.smtp.transport' => 'smtp',
                'mail.mailers.smtp.host'      => $mail['host'],
                'mail.mailers.smtp.port'      => (int) ($mail['port'] ?? 587),
                'mail.mailers.smtp.scheme'    => ($mail['encryption'] ?? 'tls') === 'ssl' ? 'smtps' : 'smtp',
                'mail.mailers.smtp.username'  => $mail['username'] ?? null,
                'mail.mailers.smtp.password'  => $password,
                'mail.from.address'           => $mail['from_address'] ?? config('mail.from.address'),
                'mail.from.name'              => $mail['from_name'] ?? config('mail.from.name'),
            ]);
        } catch (\Throwable $e) {
            // The database is not ready yet. Ignore.
        }
    }
}