<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            SettingSeeder::class,
            FormOptionSeeder::class,
            LegacyUrlSeeder::class,
        ]);

        // Compte groupe : identifiants lus dans le .env via config/braises.php (jamais en dur dans le code)
        $admin = config('braises.admin');
        if (! empty($admin['email']) && ! empty($admin['password'])) {
            User::firstOrCreate(['email' => $admin['email']], [
                'name' => $admin['name'],
                'password' => $admin['password'],
                'is_admin' => true,
            ]);
        }
    }
}
