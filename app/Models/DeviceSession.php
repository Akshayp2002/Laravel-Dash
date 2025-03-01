<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Kirschbaum\PowerJoins\PowerJoins;
use OwenIt\Auditing\Contracts\Auditable;

class DeviceSession extends Model implements Auditable
{
    use PowerJoins, HasUlids;
    use \OwenIt\Auditing\Auditable;
    //
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'device_token',
        'app_name',
        'os',
        'ip_address',
        'access_token',
        'latitude',
        'longitude',
        'last_active_at'
    ];
}
