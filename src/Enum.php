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
    private $name = null;
    private static $original_enums = null;

    protected function __construct($name, $value)
    {
        $this->name = $name;
        $this->value = $value;
    }

    private static function init()
    {
        if (!isset(static::$enums)&&!method_exists(static::class, 'enums')) {
            throw new \Exception('You must either implement static field or static method "enums".');
        }
        self::$original_enums = isset(static::$enums)?static::$enums:static::enums();
        // TODO implement check for unique names and values
        foreach (self::$original_enums as $name=>$value) {
            static::$map[''.$name] = new static($name, $value);
        }
    }

    public static function __callStatic($element, $arguments)
    {
        self::initIfNecessary();
        if (isset(static::$map[$element])) {
            return static::$map[$element];
        }
        $trace = debug_backtrace();
        trigger_error(
            sprintf("Undefined %s::%s() in %s line %s.\nKnown options are:\n%s", self::stripNS(static::class), $element, $trace[0]['file'], $trace[0]['line'], self::listDebugNames()),
            E_USER_NOTICE);
        return null;
    }

    private static function initIfNecessary()
    {
        self::verifyCorrectTraitUsage();
        if (static::$map===null) {
            static::init();
        }
    }

    private static function verifyCorrectTraitUsage()
    {
        if (!static::usesEnumerationsTrait()) {
            throw new \Exception(sprintf("You must add the trait \"macrominds\\enum\\Enumerations\" to your custom Enum.\n".
                "Example:\n\nclass %s\n{\n\tuse macrominds\\enum\\Enumerations;\n//[...]\n}", self::stripNS(static::class)));
        }
    }

    private static function usesEnumerationsTrait()
    {
        return in_array(Enumerations::class, class_uses(static::class), true);
    }

    private static function stripNS($className)
    {
        return substr($className, strrpos($className, '\\') + 1);
    }

    private static function listDebugNames()
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
    public function name()
    {
        return $this->name;
    }
    /**
    * Returns the Enum instance for this value. The value is checked strictly. That means, it will not find 0 for a boolean false and it requires you to pass exactly the type and value, you search. This may be NOT the right choice, when dealing with unknown datatypes (e.g. when working with database values).
    * @param mixed $value
    * @return mixed instance of concrete Enum class.
    * @throws \Exception if the provided value is not one of the values of the custom Enum class.
    */
    public static function fromValueStrict($value)
    {
        return self::fromValue($value, true);
    }
    /**
    * Returns the Enum instance for this value. If $strict==true, then this method behaves exactly like fromValueStrict. Otherwise it returns values that evaluate to equal $value. Using the default mode (not strict) may be the right choice, when dealing with unknown datatypes (e.g. when working with database values). However: Don't use it with Enums whose different values evaluate to equal. You'd get wrong results. Example: Don't use with an Enum that has a value of boolean false and a value of int 0, because these values cannot be distinguished in non-strict mode. In that case, use fromValueStrict and cast the value to the datatype defined in the Enum. ( fromStrictValue((int)0); ).
    * @param mixed $value
    * @return mixed instance of concrete Enum class.
    * @throws \Exception if the provided value is not one of the values of the custom Enum class.
    */
    public static function fromValue($value, $strict=false)
    {
        self::initIfNecessary();
        // don't use array_flip here, because the values may be of a type that is not allowed to be a key.
        $key = array_search($value, self::$original_enums, $strict);
        if ($key===false) {
            throw new \Exception(sprintf("Value %s doesn't represent any of the values of %s.", $value, static::class));
        }
        return static::$map[''.$key];
    }

    public function __toString()
    {
        return ''.$this->value;
    }

    public static function all()
    {
        return array_values(static::$map);
    }

    public static function values()
    {
        return array_map(function ($enumInstance) {
            return $enumInstance->value();
        }, static::all());
    }
    public static function names()
    {
        return array_keys(static::$map);
    }
}
