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

namespace macrominds\tests;

use macrominds\enum\Salutation;
use macrominds\enum\AnyValueEnum;

class EnumTest extends \PHPUnit_Framework_TestCase
{
    /** @test */
     public function it_should_provide_instances_of_the_enum()
     {
         $this->assertNotNull(Salutation::MR());
         $this->assertInstanceOf(Salutation::class, Salutation::MR());

        // same instance
        $this->assertEquals(Salutation::MR(), Salutation::MR());
        // just to be sure:
        $this->assertTrue(Salutation::MR()===Salutation::MR());
        // not same instance
        $this->assertNotEquals(Salutation::MR(), Salutation::MRS());
        // just to be sure:
        $this->assertFalse(Salutation::MR()===Salutation::MRS());
     }

     /** @test */
     public function it_should_provide_a_value()
     {
         $this->assertEquals(2, Salutation::MR()->value());
         $this->assertEquals(2, ''.Salutation::MR());
     }

     // TODO: not working in php5.6
     // see http://php.net/manual/de/language.exceptions.php#Hcom118280
     /** @test */
     public function it_cannot_be_instanciated_directly()
     {
         $this->expectException(\Error::class);
         new Salutation(4);
     }

     /** @test */
     public function it_can_be_used_for_typehinting()
     {
         $this->typeHintedFunction(Salutation::MR());
         $this->expectException(\TypeError::class);
         $this->typeHintedFunction('Mr');
     }

    public function typeHintedFunction(Salutation $salutation)
    {
        return $salutation;
    }
     /** @test */
     public function it_can_use_any_value_type()
     {
         $this->assertEquals(AnyValueEnum::String(), AnyValueEnum::String());
         $this->assertEquals(AnyValueEnum::Integer(), AnyValueEnum::Integer());
         $this->assertEquals(AnyValueEnum::Object(), AnyValueEnum::Object());

         $this->assertNotEquals(AnyValueEnum::String(), AnyValueEnum::Integer());
         $this->assertNotEquals(AnyValueEnum::Integer(), AnyValueEnum::Object());
         $this->assertNotEquals(AnyValueEnum::Object(), AnyValueEnum::String());
     }

     /** @test */
     public function it_checks_that_the_configured_names_and_values_are_unique()
     {
         $this->markTestSkipped('It currently doesn\'t check for unique names or values. At the moment, this is the responsibility of the developer of the custom enum.');
     }
}
