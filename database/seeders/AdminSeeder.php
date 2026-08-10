<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        $password = env('ADMIN_PASSWORD');
        $generated = $password === null;
        $password ??= Str::password(20);

        User::query()->updateOrCreate(
            ['username' => 'admin'],
            [
                'name' => 'Admin',
                'email' => 'admin@heartlovepics.local',
                'password' => $password,
                'is_admin' => true,
            ]
        );

        if ($generated) {
            $this->command?->warn("Generated admin password: {$password}");
            $this->command?->warn('Save this now — it is not stored anywhere and will not be shown again. Sign in at /admin and change it.');
        }
    }
}
