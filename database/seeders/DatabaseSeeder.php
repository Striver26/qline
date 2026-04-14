<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::factory()->create([
            'name' => 'Super Admin',
            'email' => 'admin@qline.my',
            'password' => Hash::make('password'),
            'role' => 'superadmin',
            'email_verified_at' => now(),
            'profile_completed' => true,
            'business_id' => null,

        ]);
    }
}
