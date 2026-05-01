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
        $email = trim((string) env('ADMIN_EMAIL', ''));
        $password = (string) env('ADMIN_PASSWORD', '');

        if ($email === '' || $password === '') {
            $this->command?->warn('Skipping admin user seed: set ADMIN_EMAIL and ADMIN_PASSWORD in your .env file.');

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
