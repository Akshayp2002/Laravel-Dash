<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        if (!User::where('email', 'admin@rugr.com')->exists()) {
            User::factory()->withPersonalTeam()->create([
                'name'     => 'Administrator',
                'email'    => 'admin@rugr.com',
                'password' => Hash::make('Admin@123'),
            ]);
        }
        if (!User::where('email', 'guest@rugr.com')->exists()) {
            User::factory()->withPersonalTeam()->create([
                'name'     => 'Guest',
                'email'    => 'guest@rugr.com',
                'password' => Hash::make('Guest@123'),
            ]);
        }

        if (!User::where('email', 'dev@rugr.com')->exists()) {
            User::factory()->withPersonalTeam()->create([
                'name'     => 'Developer',
                'email'    => 'dev@rugr.com',
                'password' => Hash::make('Dev@123'),
            ]);
        }
        if (!User::where('email', 'user@rugr.com')->exists()) {
            User::factory()->withPersonalTeam()->create([
                'name'     => 'User',
                'email'    => 'user@rugr.com',
                'password' => Hash::make('User@123'),
            ]);
        }

        // Test Users
        User::factory(10)->withPersonalTeam()->create();

    }
}
