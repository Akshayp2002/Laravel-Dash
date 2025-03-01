<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Kirschbaum\PowerJoins\PowerJoins;
use OwenIt\Auditing\Contracts\Auditable;

class Transaction extends Model implements Auditable
{
    use PowerJoins, HasUlids;
    use \OwenIt\Auditing\Auditable;
      /** @use HasFactory<\Database\Factories\TransactionFactory> */
    use HasFactory;
      /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $table = 'transactions';

    protected $fillable = [
        'id',
        'user_id',
        'account_id',
        'amount',
        'transaction_type',
        'reference_number',
        'description',
    ];

    protected $casts = [
        'id'               => 'string',
        'user_id'          => 'string',
        'account_id'       => 'string',
        'amount'           => 'decimal:2',
        'transaction_type' => 'string',
        'reference_number' => 'string',
        'description'      => 'string',
        'created_at'       => 'datetime',
        'updated_at'       => 'datetime',
    ];
}
