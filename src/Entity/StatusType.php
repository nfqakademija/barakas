<?php

namespace App\Entity;

use HappyTypes\EnumerableType;

class StatusType extends EnumerableType
{
    final public static function newRequest()
    {
        return self::get(0, 'Naujas');
    }
}
