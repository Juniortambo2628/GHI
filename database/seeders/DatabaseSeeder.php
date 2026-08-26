<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'admin@globalharmonyinitiative.com'],
            [
                'name' => 'Admin',
                'password' => 'password',
                'email_verified_at' => now(),
                'is_admin' => true,
            ]
        );
    }
}
