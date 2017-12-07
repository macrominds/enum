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

trait Enumerations
{
    private $value = null;
    private $name = null;

    private static $delegatee = null;

    private function __construct($name, $value)
    {
        $this->name = $name;
        $this->value = $value;
    }

    public function value()
    {
        return $this->value;
    }
    public function name()
    {
        return $this->name;
    }

    public function __toString()
    {
        return ''.$this->value;
    }

    private static function getDelegatee()
    {
        return self::$delegatee?:self::initDelegatee();
    }

    private static function initDelegatee()
    {
        static::validateCorrectImplementation();
        self::$delegatee = new Delegatee(static::class, self::getSpecifiedEnums(), function ($name, $value) {
            return new static($name, $value);
        });
        return self::$delegatee;
    }

    private static function validateCorrectImplementation()
    {
        if (!isset(static::$enums)&&!method_exists(static::class, 'enums')) {
            throw new \Exception(sprintf('You must either implement static field or static method "enums" in %s.', static::class));
        }
    }
    private static function getSpecifiedEnums()
    {
        return isset(static::$enums)?static::$enums:static::enums();
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
        return self::getDelegatee()->fromValue($value, $strict);
    }

    public static function all()
    {
        return self::getDelegatee()->all();
    }

    public static function values()
    {
        return self::getDelegatee()->values();
    }
    public static function names()
    {
        return self::getDelegatee()->names();
    }

    public static function __callStatic($element, $arguments)
    {
        if ($element==='enums') {
            return null;
        }
        try {
            return self::fromKey($element);
        } catch (\Exception $e) {
            $trace = debug_backtrace();
            trigger_error(
                sprintf("Undefined %s::%s() in %s line %s.\nKnown options are:\n%s", self::stripNS(static::class), $element, $trace[0]['file'], $trace[0]['line'], self::listDebugNames()),
                E_USER_NOTICE);
            return null;
        }
    }

    /**
     * Returns the Enum instance for this key. So, if your enum Salutation defines 'MR' => 2 and you call Salutation::fromKey('MR'), you will receive an instance equal to Salutation::MR().
     * Use this when you're dealing with variables that represent your keys. An Exception will be thrown if the key doesn't match the Enum (e.g. Salutation::fromKey('non-existing')).
     * @param string $key the key. Example: if your enum Color defines 'green' => '#00ff00', then Color::fromKey('green')->value() will be '#00ff00'
     * @return mixed instance of concrete Enum class
     * @throws \Exception if the key doesn't match the Enum constraints
     */
    public static function fromKey($key)
    {
        $delegatee = self::getDelegatee();
        $result = $delegatee->get($key);
        if($result === null) {
            throw new \Exception(sprintf("Undefined %s::%s().\nKnown options are:\n%s", self::stripNS(static::class), $key, self::listDebugNames()));
        }
        return $result;
    }
    private static function listDebugNames()
    {
        $className = self::stripNS(static::class);

        return array_reduce(array_keys(self::getSpecifiedEnums()),
            function ($carry, $name) use ($className) {
                return sprintf("%s* %s::%s()\n", $carry, $className, $name);
            });
    }
    private static function stripNS($className)
    {
        return substr($className, strrpos($className, '\\') + 1);
    }
    public static function isInitialized()
    {
        return self::$delegatee!==null;
    }
}
