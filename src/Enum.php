<?php

/*
 * The MIT License
 *
 * Copyright 2016 Thomas Praxl <thomas@macrominds.de>.
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 */


namespace macrominds\enum;

abstract class Enum
{
    private $value = null;

    protected function __construct($value)
    {
        $this->value = $value;
    }

    private static function init()
    {
        if (!isset(static::$enums)&&!method_exists(static::class, 'enums')) {
            throw new \Exception('You must either implement static field or static method "enums".');
        }
        $enums = isset(static::$enums)?static::$enums:static::enums();
        // TODO implement check for unique names and values
        foreach ($enums as $name=>$value) {
            static::$map[''.$name] = new static($value);
        }
    }

    public static function __callStatic($element, $arguments)
    {
        if (!in_array(Enumerations::class, class_uses(static::class), true)) {
            throw new \Exception(sprintf("You must add the trait \"macrominds\\enum\\Enumerations\" to your custom Enum.\n".
                "Example:\n\nclass %s\n{\n\tuse macrominds\\enum\\Enumerations;\n//[...]\n}", self::stripNS(static::class)));
        }
        if (static::$map===null) {
            static::init();
        }
        if (isset(static::$map[$element])) {
            return static::$map[$element];
        }
        $trace = debug_backtrace();
        trigger_error(
            sprintf("Undefined %s::%s() in %s line %s.\nKnown options are:\n%s", self::stripNS(static::class), $element, $trace[0]['file'], $trace[0]['line'], self::listNames()),
            E_USER_NOTICE);
        return null;
    }

    private static function stripNS($className)
    {
        return substr($className, strrpos($className, '\\') + 1);
    }

    public static function listNames()
    {
        $className = self::stripNS(static::class);

        return array_reduce(array_keys(static::$map),
            function ($carry, $name) use ($className) {
                return sprintf("%s* %s::%s()\n", $carry, $className, $name);
            });
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
