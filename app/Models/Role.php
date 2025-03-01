<?php

namespace App\Models;

use App\Enum\Permissions\DefaultRoleEnum;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Kirschbaum\PowerJoins\PowerJoins;
use OwenIt\Auditing\Contracts\Auditable;
use Spatie\Permission\Models\Role as SpatieRole;

class Role extends SpatieRole implements Auditable
{
    use PowerJoins, HasUlids;
    use \OwenIt\Auditing\Auditable;
    //
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */

    protected $table = 'roles';

    protected $fillable = [
        'name',
        'guard_name'
    ];

    public static function fetchAllDefaultStaticRoles(): array
    {
        return [
            DefaultRoleEnum::labels(),
        ];
    }

    public static function fetchAllDefaultStaticRoleKeys(): array
    {
        return [
            DefaultRoleEnum::cases(),
        ];
    }
}
