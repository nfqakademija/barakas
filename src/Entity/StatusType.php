<?php

namespace App\Entity;

use HappyTypes\EnumerableType;

class StatusType extends EnumerableType
{
    final public static function posted()
    {
        return self::get(0, 'posted');
    }

    final public static function pending()
    {
        return self::get(1, 'pending');
    }

    final public static function approved()
    {
        return self::get(2, 'approved');
    }
}
