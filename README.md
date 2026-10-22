# MyBlog

A multi-user blog built with **Laravel 12**, **MySQL** and **Bootstrap 5**.
Authors write posts, admins approve or reject them, and everything is tracked in an activity log.

## Features

**Blog**
- Posts with draft, pending, published and rejected status
- Admin approval workflow with an optional rejection reason
- Schedule posts for a future date
- Categories, tags, comments, trash and restore
- SEO fields: meta title, description, keywords and image
- Sitemap, RSS feed and robots.txt
- Notifications (bell icon) for authors and admins
- Dark mode and Google login

**Admin panel**
- Roles and permissions (create your own roles)
- Assign roles to users
- Activity log (logins and all post changes, with search and filter)
- SMTP settings and a test email page
- Weekly summary email to every admin

## Requirements

- PHP 8.2 or higher
- Composer
- MySQL (XAMPP works well)

## Setup

```bash
git clone https://github.com/boralugoda02/myblog.git
cd myblog
composer install
cp .env.example .env
php artisan key:generate
```

On Windows Command Prompt use `copy .env.example .env` instead of `cp`.

1. Create a MySQL database named `blog_app_db`.
2. Open `.env` and set your database details:

```
DB_DATABASE=blog_app_db
DB_USERNAME=root
DB_PASSWORD=
```

3. Run the migrations and start the server:

```bash
php artisan migrate
php artisan storage:link
php artisan serve
```

Open http://127.0.0.1:8000

## Create an admin user

1. Register a normal account on the site.
2. Make it an admin:

```bash
php artisan tinker
```

```php
App\Models\User::where('email', 'your-email@example.com')->update(['role' => 'admin']);
```

3. Log in again. The **Admin** menu appears in the sidebar.

## Email settings

Set your SMTP details in `.env` (or in **Admin > SMTP Settings**):

```
MAIL_MAILER=smtp
MAIL_HOST=smtp.example.com
MAIL_PORT=587
MAIL_USERNAME=your-username
MAIL_PASSWORD=your-password
MAIL_FROM_ADDRESS="you@example.com"
MAIL_FROM_NAME="${APP_NAME}"
```

For testing, a free Mailtrap inbox is the easiest option.

## Weekly summary email

```bash
php artisan blog:weekly-summary --dry-run   # preview only
php artisan blog:weekly-summary             # send now
```

It is scheduled for every Monday at 8:30 AM. To run it automatically, make the scheduler run every minute:

```bash
php artisan schedule:run
```

On Windows, use Task Scheduler (Program: `C:\xampp\php\php.exe`, Arguments: `artisan schedule:run`, Start in: your project folder, repeat every 1 minute).

## Project structure (main parts)

| Path | What it does |
|---|---|
| `app/Http/Controllers/Admin` | Admin panel controllers (roles, permissions, activity log) |
| `app/Observers/PostObserver.php` | Writes post changes to the activity log |
| `app/Models/ActivityLog.php` | Activity log model |
| `app/Console/Commands/SendWeeklySummary.php` | Weekly summary command |
| `resources/views/admin` | Admin panel pages |
| `routes/web.php` | All web routes |
| `routes/console.php` | Scheduled tasks |

## Notes

- Never commit your `.env` file. It is already listed in `.gitignore`.
- Uploaded images are stored in `storage/app/public`. Run `php artisan storage:link` once so they show on the site.
