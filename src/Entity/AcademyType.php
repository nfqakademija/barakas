<?php


namespace App\Entity;

use HappyTypes\EnumerableType;

class AcademyType extends EnumerableType
{
    final public static function university()
    {
        return self::get(0, 'university');
    }

    final public static function college()
    {
        return self::get(1, 'college');
    }

    final public static function technical()
    {
        return self::get(2, 'technical');
    }
}
