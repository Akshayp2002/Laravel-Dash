<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
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
        User::factory(10)->withPersonalTeam()->create();

        // Check if 'Developer' user exists before creating it
        if (!User::where('email', 'developer@yopmail.com')->exists()) {
            User::factory()->withPersonalTeam()->create([
                'name'     => 'Developer',
                'email'    => 'developer@yopmail.com',
                'password' => Hash::make('password'),   // Default password
            ]);
        }
        if (!User::where('email', 'user@yopmail.com')->exists()) {
            User::factory()->withPersonalTeam()->create([
                'name'     => 'User',
                'email'    => 'user@yopmail.com',
                'password' => Hash::make('password'),   // Default password
            ]);
        }
    }
}
