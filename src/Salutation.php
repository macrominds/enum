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

namespace macrominds\enums;

class Salutation
{
    private $value = null;

    public static $map;

    protected function __construct($value)
    {
        $this->value = $value;
    }

    public static function init()
    {
        self::$map = [
            'MRS' => new static(1),
            'MR' => new static(2),
            'MS' => new static(3)
        ];
    }

    public static function __callStatic($element, $arguments)
    {
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
Salutation::init();
