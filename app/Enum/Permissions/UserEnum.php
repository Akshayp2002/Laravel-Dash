<?php

namespace App\Enum\Permissions;

enum UserEnum: string
{
    case User            = 'user';
    case UserViewAny     = 'user.view-any';
    case UserView        = 'user.view';
    case UserCreate      = 'user.create';
    case UserUpdate      = 'user.update';
    case UserDelete      = 'user.delete';
    case UserRestore     = 'user.restore';
    case UserForceDelete = 'user.force-delete';

    public function label(): string
    {
        return match ($this) {
            self::User            => "User Full Access",
            self::UserViewAny     => "User List Access",
            self::UserView        => "User View Access",
            self::UserCreate      => "User Create Access",
            self::UserUpdate      => "User Update Access",
            self::UserDelete      => "User Delete Access",
            self::UserRestore     => "User Restore Access",
            self::UserForceDelete => "User Force Delete Access",
        };
    }

    public static function labels(): array
    {
        return [
            'label'       => 'User Permissions',
            'permissions' => array_reduce(self::cases(), function ($carry, $enum) {
                                $carry[$enum->value] = $enum->label();
                                return $carry;
                            }, []),
        ];
    }
}
