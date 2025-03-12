<?php

namespace Database\Seeders;

use App\Enum\Permissions\DefaultRoleEnum;
use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;

class AuthorizeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        /**========================================================================
         *                           ROLE HAS PERMISSION
         *========================================================================**/

        $role = Role::where('name', DefaultRoleEnum::Administrator)->where('guard_name', 'api')->first();
        $role->syncPermissions(Permission::fetchAllStaticPermissionKeysWithoutGroup());

        /**========================================================================
         *                           USER HAS ROLE
         *========================================================================**/

            $administrator = User::where('email', 'admin@rugr.com')->first();
            $developer     = User::where('email', 'dev@rugr.com')->first();
            $guest         = User::where('email', 'guest@rugr.com')->first();
            $user          = User::where('email', 'user@rugr.com')->first();

            $administrator->syncRoles([DefaultRoleEnum::Administrator]);
            $developer->syncRoles([DefaultRoleEnum::Administrator]);
            $guest->syncRoles([DefaultRoleEnum::Guest]);
            $user->syncRoles([DefaultRoleEnum::Guest]);



        /**========================================================================
         *                           USER HAS PERMISSION
         *========================================================================**/


        // $developer     = User::where('email', 'developer@rugr.com')->first();
        // $developer->givePermissionTo('');

    }
}
