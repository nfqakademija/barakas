<?php

namespace App\Entity;

use HappyTypes\EnumerableType;

class AchievementType extends EnumerableType
{
    final public static function firstAid()
    {
        return self::get(1, 'first_aid');
    }

    final public static function tenHelpProvided()
    {
        return self::get(2, 'ten_help_provided');
    }

    final public static function twentyHelpProvided()
    {
        return self::get(3, 'twenty_help_provided');
    }

    final public static function thousandPoints()
    {
        return self::get(4, 'thousand_points');
    }

    final public static function twoThousandPoints()
    {
        return self::get(5, 'two_thousand_points');
    }

    final public static function fiveThousandPoints()
    {
        return self::get(6, 'five_thousand_points');
    }

    final public static function tenThousandPoints()
    {
        return self::get(7, 'ten_thousand_points');
    }

    final public static function tenMessages()
    {
        return self::get(8, 'ten_messages');
    }

    final public static function twentyMessages()
    {
        return self::get(9, 'twenty_messages');
    }

    final public static function thirtyMessages()
    {
        return self::get(10, 'thirty_messages');
    }
}
