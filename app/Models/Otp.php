<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Kirschbaum\PowerJoins\PowerJoins;
use OwenIt\Auditing\Contracts\Auditable;
use Illuminate\Database\Eloquent\Concerns\HasUlids;

class Otp extends Model implements Auditable
{
    use PowerJoins;
    use \OwenIt\Auditing\Auditable;
    use HasUlids;
    //
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'otp',
        'module',
        'expires_at',
    ];
}
