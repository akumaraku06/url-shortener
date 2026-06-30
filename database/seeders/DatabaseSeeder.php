<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->seedSuperAdmin();
        $this->seedDemoCompanyAndUsers();
    }

    /**
     * "Create a SuperAdmin account using a Database Seeder."
     *
     * Inserted with a raw SQL statement.
     */
    private function seedSuperAdmin(): void
    {
        $email = 'superadmin@example.com';

        $exists = DB::selectOne('SELECT id FROM users WHERE email = ?', [$email]);

        if ($exists) {
            return;
        }

        $now = now()->toDateTimeString();
        $hashedPassword = Hash::make('password');

        DB::insert(
            'INSERT INTO users (name, email, password, company_id, role, created_at, updated_at)
             VALUES (?, ?, ?, ?, ?, ?, ?)',
            ['Super Admin', $email, $hashedPassword, null, 'superadmin', $now, $now]
        );
    }

    /**
     * Two demo companies with an Admin and a Member each, so the app has
     * something to log in with out of the box.
     *
     * Login password for every seeded account below is: password
     */
    private function seedDemoCompanyAndUsers(): void
    {
        $companyId = DB::table('companies')->insertGetId([
            'name' => 'Acme Inc',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $secondCompanyId = DB::table('companies')->insertGetId([
            'name' => 'Globex Corp',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $password = Hash::make('password');

        DB::table('users')->insert([
            [
                'name' => 'Acme Admin',
                'email' => 'admin@acme.test',
                'password' => $password,
                'company_id' => $companyId,
                'role' => 'admin',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Acme Member',
                'email' => 'member@acme.test',
                'password' => $password,
                'company_id' => $companyId,
                'role' => 'member',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Globex Admin',
                'email' => 'admin@globex.test',
                'password' => $password,
                'company_id' => $secondCompanyId,
                'role' => 'admin',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Globex Member',
                'email' => 'member@globex.test',
                'password' => $password,
                'company_id' => $secondCompanyId,
                'role' => 'member',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
