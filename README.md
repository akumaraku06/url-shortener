# URL Shortener (Laravel)

A multi-tenant URL shortener built for the  Tech backend developer assignment.

## Stack

- PHP 8.2+ / Laravel 10
- SQLite or MySQL
- Plain Blade views, styled to resemble the provided sample dashboard mockup

## Roles & Rules implemented

- Roles: `SuperAdmin`, `Admin`, `Member`
- A `SuperAdmin` is seeded via a Database Seeder using **raw SQL** in `database/seeders/DatabaseSeeder.php` (`DB::insert(...)`, not Eloquent).
- One company has many users; a `SuperAdmin` belongs to no company (`company_id` is `null`).
- **Invitations**
  - `SuperAdmin` can invite an `Admin` into a new company.
  - `Admin` can invite another `Admin` or a `Member` into their own company.
- **Short URLs**
  - `Admin` and `Member` can create short urls.
  - `SuperAdmin` cannot create short urls.
  - `SuperAdmin` can see the list of all short urls for every company.
  - `Admin` can only see the list of short urls created in their own company.
  - `Member` can only see the list of short urls created by themselves.
  - All short urls are publicly resolvable (`/s/{code}`) and redirect to the original url — no authentication required to follow a short link.

## Local Setup

```bash
# 1. Clone the repo
git clone <repo-url> url-shortener
cd url-shortener

# 2. Install PHP dependencies
composer install

# 3. Copy the environment file
cp .env.example .env

# 4. Generate the app key
php artisan key:generate

# 5. Run migrations and seed the database
php artisan migrate --seed

# 6. Serve the application
php artisan serve
```

Visit `http://127.0.0.1:8000` — you'll be redirected to `/login`.

### Using MySQL

Edit `.env`:

```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=url_shortener
DB_USERNAME=root
DB_PASSWORD=
```

Then create the `url_shortener` database in MySQL and run `php artisan migrate --seed`.

## Seeded accounts

All seeded accounts use the password: `password`

| Email | Role | Company |
|---|---|---|
| superadmin@example.com | SuperAdmin | — |
| admin@acme.test | Admin | Acme Inc |
| member@acme.test | Member | Acme Inc |
| admin@globex.test | Admin | Globex Corp |
| member@globex.test | Member | Globex Corp |

This covers:

- Admin and Member can create short urls
- SuperAdmin cannot create short urls
- SuperAdmin can view the short url list for every company
- Admin can only see short urls created in their own company
- Member can only see short urls created by themselves
- Short urls are publicly resolvable and redirect to the original url
- SuperAdmin can invite an Admin; Admin can invite an Admin or a Member

## Project structure highlights

```
app/Http/Controllers/AuthController.php        Login / logout
app/Http/Controllers/CompanyController.php     SuperAdmin: create companies + their first Admin
app/Http/Controllers/InvitationController.php  Invitation create/send/accept
app/Http/Controllers/ShortUrlController.php    Create + scoped listing + public redirect
app/Http/Controllers/DashboardController.php   Role-specific combined dashboard
app/Http/Middleware/EnsureRole.php             Generic `role:x,y` route middleware
app/Models/{User,Company,Invitation,ShortUrl}.php
database/seeders/DatabaseSeeder.php            Raw-SQL SuperAdmin insert + demo data
database/migrations/                           companies, users (+company_id/role), invitations, short_urls
routes/web.php                                 All routes, grouped by role middleware
resources/views/                               Blade templates styled like the sample dashboard mockup
tests/Feature/                                 Feature tests for the required scenarios
```

## AI Tool Usage Disclosure
Claude and Chatgpt
Per the assignment's Acceptable AI Usage Policy:

- Claude (Anthropic) was used to scaffold the Laravel project structure (migrations, models, controllers, routes, Blade views) and to iterate on the dashboard UI to match the provided sample mockup.
- All resulting code was reviewed and the role/permission logic was deliberately specified and verified against the assignment requirements rather than accepted blindly.
"# url-shortener" 
"# url-shortener" 
