<?php

namespace Database\Seeders;

use App\Models\Admin;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    // First admin login — backend_details.md §9 ("A way to create the first
    // admin login"). Credentials come from env so nothing plaintext is
    // committed; change ADMIN_SEED_PASSWORD in .env before running this in
    // any shared environment, and rotate it immediately after first login.
    public function run(): void
    {
        $username = env('ADMIN_SEED_USERNAME', 'admin');
        $password = env('ADMIN_SEED_PASSWORD');

        if (! $password) {
            $this->command?->warn(
                'ADMIN_SEED_PASSWORD is not set in .env — skipping admin seeding. '.
                'Set it and re-run: php artisan db:seed --class=AdminSeeder'
            );

            return;
        }

        Admin::firstOrCreate(
            ['username' => $username],
            [
                'password_hash' => Hash::make($password),
                'full_name' => env('ADMIN_SEED_FULL_NAME', 'Administrator'),
                'role' => 'super_admin',
                'org_code' => null, // access to both gt and ga
            ]
        );
    }
}
