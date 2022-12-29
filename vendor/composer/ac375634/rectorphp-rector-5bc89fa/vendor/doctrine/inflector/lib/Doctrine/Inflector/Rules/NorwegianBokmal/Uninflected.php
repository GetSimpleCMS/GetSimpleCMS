<?php

declare (strict_types=1);
namespace RectorPrefix202212\Doctrine\Inflector\Rules\NorwegianBokmal;

use RectorPrefix202212\Doctrine\Inflector\Rules\Pattern;
final class Uninflected
{
    /** @return Pattern[] */
    public static function getSingular() : iterable
    {
        yield from self::getDefault();
    }
    /** @return Pattern[] */
    public static function getPlural() : iterable
    {
        yield from self::getDefault();
    }
    /** @return Pattern[] */
    private static function getDefault() : iterable
    {
        (yield new Pattern('barn'));
        (yield new Pattern('fjell'));
        (yield new Pattern('hus'));
    }
}
