<?php

namespace App\Helpers;

use App\Types\RoleType;

class AppHelpers
{
    public static function getRoleMatrix()
    {
        return [
            RoleType::USER       => [RoleType::USER, RoleType::EDITOR, RoleType::ADMIN, RoleType::SUPERADMIN],
            RoleType::EDITOR     => [RoleType::EDITOR, RoleType::ADMIN, RoleType::SUPERADMIN],
            RoleType::ADMIN      => [RoleType::ADMIN, RoleType::SUPERADMIN],
            RoleType::SUPERADMIN => [RoleType::SUPERADMIN],
        ];
    }
}