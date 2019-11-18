<?php

namespace App\Entity;

use HappyTypes\EnumerableType;

class StatusType extends EnumerableType
{
    final public static function urgent()
    {
        return self::get(0, 'urgent');
    }

    final public static function normal()
    {
        return self::get(1, 'normal');
    }
}
