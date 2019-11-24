<?php

namespace App\Entity;

use HappyTypes\EnumerableType;

class SolvedType extends EnumerableType
{
    final public static function notSolved()
    {
        return self::get(0, 'not_solved');
    }
    final public static function solved()
    {
        return self::get(1, 'solved');
    }
}
