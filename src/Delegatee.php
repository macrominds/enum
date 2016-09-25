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

class Delegatee
{
    
    private $enums = null;
    private $class = null;
    private $instanceMap = null;

    public function __construct($class, $enums, \Closure $createEnumInstance)
    {
        $this->class=$class;
        $this->enums = $enums;
        foreach ($enums as $name=>$value) {
            $this->instanceMap[''.$name] = call_user_func($createEnumInstance, $name, $value);
        }
    }
    public function get($name)
    {
        if (isset($this->instanceMap[$name])) {
            return $this->instanceMap[$name];
        }
        return null;
    }
    /**
    * Returns the Enum instance for this value. The value is checked strictly. That means, it will not find 0 for a boolean false and it requires you to pass exactly the type and value, you search. This may be NOT the right choice, when dealing with unknown datatypes (e.g. when working with database values).
    * @param mixed $value
    * @return mixed instance of concrete Enum class.
    * @throws \Exception if the provided value is not one of the values of the custom Enum class.
    */
    public function fromValueStrict($value)
    {
        return $this->fromValue($value, true);
    }
    /**
    * Returns the Enum instance for this value. If $strict==true, then this method behaves exactly like fromValueStrict. Otherwise it returns values that evaluate to equal $value. Using the default mode (not strict) may be the right choice, when dealing with unknown datatypes (e.g. when working with database values). However: Don't use it with Enums whose different values evaluate to equal. You'd get wrong results. Example: Don't use with an Enum that has a value of boolean false and a value of int 0, because these values cannot be distinguished in non-strict mode. In that case, use fromValueStrict and cast the value to the datatype defined in the Enum. ( fromStrictValue((int)0); ).
    * @param mixed $value
    * @return mixed instance of concrete Enum class.
    * @throws \Exception if the provided value is not one of the values of the custom Enum class.
    */
    public function fromValue($value, $strict=false)
    {
        // don't use array_flip here, because the values may be of a type that is not allowed to be a key.
        $key = array_search($value, $this->enums, $strict);
        if ($key===false) {
            throw new \Exception(sprintf("Value %s doesn't represent any of the values of %s.", $value, $this->class));
        }
        return $this->instanceMap[''.$key];
    }

    public function all()
    {
        return array_values($this->instanceMap);
    }

    public function values()
    {
        return array_map(function ($enumInstance) {
            return $enumInstance->value();
        }, $this->all());
    }
    public function names()
    {
        return array_keys($this->instanceMap);
    }

    public function buildInstanceMap($enums, Closure $constructClosure)
    {
        // TODO implement check for unique names and values
        foreach ($enums as $name=>$value) {
            $this->instanceMap[''.$name] = new $constructClosure($name, $value);
        }
    }
    
}
