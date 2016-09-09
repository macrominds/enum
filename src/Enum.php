<?php

namespace macrominds\enums;

abstract class Enum
{
    private $value = null;

    private static $map = null;

    protected function __construct($value)
    {
        $this->value = $value;
    }

    private static function init()
    {
        // TODO implement check for unique names and values
        foreach (static::$enums as $name=>$value) {
            self::$map[''.$name] = new static($value);
        }
    }

    public static function __callStatic($element, $arguments)
    {
        if (self::$map===null) {
            static::init();
        }
        if (isset(self::$map[$element])) {
            return self::$map[$element];
        }
        $trace = debug_backtrace();
        trigger_error(
            'Undefined Enum value '.$element.' for ' . static::class .
            ' in ' . $trace[0]['file'] .
            ' line ' . $trace[0]['line'],
            E_USER_NOTICE);
        return null;
    }

    public function value()
    {
        return $this->value;
    }

    public function __toString()
    {
        return ''.$this->value;
    }
}
