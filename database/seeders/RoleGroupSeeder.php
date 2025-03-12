<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\RoleGroup;

class RoleGroupSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        $roleGroup = [
            [
                'name'       => 'group-a',
                'label'      => 'Group A',
                'guard_name' => 'api',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name'       => 'group-b',
                'label'      => 'Group B',
                'guard_name' => 'api',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name'       => 'group-c',
                'label'      => 'Group C',
                'guard_name' => 'api',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name'       => 'group-d',
                'label'      => 'Group D',
                'guard_name' => 'api',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name'       => 'group-e',
                'label'      => 'Group E',
                'guard_name' => 'web',
                'created_at' => now(),
                'updated_at' => now()
            ],
        ];

        RoleGroup::insert($roleGroup);


    }
}
