<?php

namespace Database\Seeders;

use App\Models\User;
use BezhanSalleh\FilamentShield\Support\Utils;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Use config(), not env(): when `config:cache` is enabled, env() outside config files returns null/falsey.
        $email = trim((string) config('planance.admin_email', ''));
        $password = (string) config('planance.admin_password', '');

        if ($email === '' || $password === '') {
            $this->command?->warn('Skipping admin user seed: set ADMIN_EMAIL and ADMIN_PASSWORD in `.env`, then run `php artisan config:clear` (or rebuild `config:cache`) and seed again.');

            return;
        }

        $guardName = config('auth.defaults.guard');

        Role::firstOrCreate(
            [
                'name' => Utils::getSuperAdminName(),
                'guard_name' => $guardName,
            ],
        );

        $user = User::updateOrCreate(
            ['email' => $email],
            [
                'name' => 'Administrator',
                'password' => $password,
                'email_verified_at' => now(),
                'ai_access' => true,
            ],
        );

        $user->assignRole(Utils::getSuperAdminName());

        $this->command?->info("Admin user ensured: {$email}");
    }
}
