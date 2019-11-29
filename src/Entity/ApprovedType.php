<?php

namespace App\Entity;

use HappyTypes\EnumerableType;

class ApprovedType extends EnumerableType
{
    final public static function notApproved()
    {
        return self::get(0, 'not_approved');
    }
    final public static function approved()
    {
        return self::get(1, 'approved');
    }
}
