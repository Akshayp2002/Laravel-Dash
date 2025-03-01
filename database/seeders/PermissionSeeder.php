<?php

namespace Database\Seeders;

use App\Enum\Permissions\TransactionEnum;
use App\Enum\Permissions\UserEnum;
use App\Models\Permission;
use Illuminate\Database\Seeder;

class PermissionSeeder extends Seeder
{

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Give all permission enum for insert to DB
        $permissions = array_merge(
            UserEnum::cases(),
            TransactionEnum::cases(),
        );




        $permissionsData = array_map(fn($enum) =>
            [
                'name' => $enum->value,
                'guard_name' => 'web',
                'created_at' => now(),
                'updated_at' => now()
            ], $permissions);

        Permission::insert($permissionsData);
    }                                                
}
