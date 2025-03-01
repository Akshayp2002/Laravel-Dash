<?php

namespace App\Enum\Permissions;


enum TransactionEnum : string
{

    case Transaction            = 'transaction';
    case TransactionViewAny     = 'transaction.view-any';
    case TransactionView        = 'transaction.view';
    case TransactionCreate      = 'transaction.create';
    case TransactionUpdate      = 'transaction.update';
    case TransactionDelete      = 'transaction.delete';
    case TransactionRestore     = 'transaction.restore';
    case TransactionForceDelete = 'transaction.force-delete';

    public function label(): string
    {
        return match ($this) {
            self::Transaction            => "Transaction Full Access",
            self::TransactionViewAny     => "Transaction List Access",
            self::TransactionView        => "Transaction View Access",
            self::TransactionCreate      => "Transaction Create Access",
            self::TransactionUpdate      => "Transaction Update Access",
            self::TransactionDelete      => "Transaction Delete Access",
            self::TransactionRestore     => "Transaction Restore Access",
            self::TransactionForceDelete => "Transaction Force Delete Access",
        };
    }

    public static function labels(): array
    {
        return [
            'label'       => 'Transaction Permissions',
            'permissions' => array_reduce(self::cases(), function ($carry, $enum) {
                $carry[$enum->value] = $enum->label();
                return $carry;
            }, []),
        ];
    }
}
