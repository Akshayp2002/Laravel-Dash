<?php

namespace App\Models;

use App\Enum\Permissions\UserEnum;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Kirschbaum\PowerJoins\PowerJoins;
use OwenIt\Auditing\Contracts\Auditable;
use Spatie\Permission\Models\Permission as SpatiePermission;
use App\Enum\Permissions\TransactionEnum;

class Permission extends SpatiePermission implements Auditable
{
    use PowerJoins, HasUlids;
    use \OwenIt\Auditing\Auditable;
    //
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */


    protected $table = 'permissions';

    protected $fillable = [
        'name',
        'guard_name'
    ];

    public static function fetchAllStaticPermissions() : array {
        return [
            UserEnum::labels(),
            TransactionEnum::labels(),
        ];
    }

    public static function fetchAllStaticPermissionKeys(): array
    {
        return [
            UserEnum::cases(),
            TransactionEnum::cases(),
        ];
    }


    public static function fetchAllStaticPermissionKeysWithoutGroup(): array
    {
        $data = self::fetchAllStaticPermissionKeys();
        return array_merge(...$data);
    }
}
