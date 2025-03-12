<?php

namespace Database\Seeders;

use App\Enum\Permissions\DefaultRoleEnum;
use App\Models\Role;
use App\Models\RoleGroup;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roles = array_map(fn($enum) =>
        [
            'name'       => $enum->value,
            'guard_name' => 'api',
            'created_at' => now(),
            'updated_at' => now()
        ], DefaultRoleEnum::cases());

        Role::insert($roles);

        // // Custom Role for test data
        // Role::create([
        //     'name'     => DefaultRoleEnum::Administrator,
        //     'grorp_id' => RoleGroup::where('name','group-a')->first()->id,
        // ]);
        // Role::create([
        //     'name'     => DefaultRoleEnum::Guest,
        //     'grorp_id' => RoleGroup::where('name', 'group-a')->first()->id,
        // ]);


        // Role::create([
        //     'name'     => DefaultRoleEnum::Administrator,
        //     'grorp_id' => RoleGroup::where('name', 'group-b')->first()->id,
        // ]);
        // Role::create([
        //     'name'     => DefaultRoleEnum::Guest,
        //     'grorp_id' => RoleGroup::where('name', 'group-b')->first()->id,
        // ]);
    }
}
