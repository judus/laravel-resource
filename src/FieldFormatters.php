<?php

namespace Maduser\Laravel\Resource;

class FieldFormatters
{
    protected static $plugins;

    public static function register(string $class)
    {
        self::$plugins || self::$plugins = collect();
        self::$plugins->put($class, $class::getLabel());
    }

    public static function all()
    {
        return self::$plugins;
    }

}
