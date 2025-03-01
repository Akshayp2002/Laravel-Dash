<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Stancl\Tenancy\Database\Models\Tenant as BaseTenant;
use Stancl\Tenancy\Contracts\TenantWithDatabase;
use Stancl\Tenancy\Database\Concerns\HasDatabase;
use Stancl\Tenancy\Database\Concerns\HasDomains;
use Kirschbaum\PowerJoins\PowerJoins;

class Tenant extends BaseTenant implements TenantWithDatabase
{
    use PowerJoins, HasUlids, HasDatabase, HasDomains;
    //
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [];
}
