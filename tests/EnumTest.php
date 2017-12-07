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

use macrominds\enum\Delegatee;
use macrominds\enum\Salutation;
use macrominds\enum\Color;
use macrominds\enum\AnyValueEnum;
use macrominds\enum\FalsyValues;
use macrominds\enum\invalid\InvalidInstanceField;
use macrominds\enum\invalid\InvalidInstanceMethod;
use macrominds\enum\invalid\InvalidMissingFieldAndMethod;

class EnumTest extends \PHPUnit_Framework_TestCase
{
    /** @test */
     public function it_should_provide_instances_of_the_enum()
     {
         $this->assertNotNull(Salutation::MR());
         $this->assertInstanceOf(Salutation::class, Salutation::MR());

        // same instance
        $this->assertSame(Salutation::MR(), Salutation::MR());
        // just to be sure:
        $this->assertTrue(Salutation::MR()===Salutation::MR());
        // not same instance
        $this->assertNotSame(Salutation::MR(), Salutation::MRS());
        // just to be sure:
        $this->assertFalse(Salutation::MR()===Salutation::MRS());
     }

     /** @test */
     public function it_should_provide_a_value()
     {
         $this->assertEquals(2, Salutation::MR()->value());
         $this->assertEquals(2, ''.Salutation::MR());
     }

     /** @test */
     public function it_should_be_fetchable_by_using_a_value()
     {
         $this->assertSame(Salutation::fromValue(2), Salutation::MR());
     }

     /** @test */
     public function it_should_throw_an_exception_when_from_value_is_called_with_a_nonexisting_value()
     {
         $this->expectException(\Exception::class);
        
         $invalidValue = 56;
         Salutation::fromValue($invalidValue);
     }

     /** @test */
     public function it_should_provide_the_correct_enum_instance_in_strict_mode()
     {
         $nonStrictZero = FalsyValues::fromValue(0);
         $nonStrictFalse = FalsyValues::fromValue(false);
        // Beware non strict mode for values that evaluate to equal.
        // The following behavior is known, but may not be desired!
        $this->assertSame($nonStrictZero, $nonStrictFalse);

        // Use strict mode instead
        $this->assertSame(FalsyValues::Boolean(), FalsyValues::fromValueStrict(false));
         $this->assertSame(FalsyValues::Integer(), FalsyValues::fromValueStrict(0));
     }
     // TODO: not working in php5.6
     // see http://php.net/manual/de/language.exceptions.php#Hcom118280
     /** @test */
     public function it_cannot_be_instanciated_directly()
     {
         $this->expectException(\Error::class);
         new Salutation('MRX', 4);
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
         $this->assertSame(AnyValueEnum::String(), AnyValueEnum::String());
         $this->assertSame(AnyValueEnum::Integer(), AnyValueEnum::Integer());
         $this->assertSame(AnyValueEnum::Object(), AnyValueEnum::Object());

         $this->assertNotSame(AnyValueEnum::String(), AnyValueEnum::Integer());
         $this->assertNotSame(AnyValueEnum::Integer(), AnyValueEnum::Object());
         $this->assertNotSame(AnyValueEnum::Object(), AnyValueEnum::String());
     }

     /** @test */
     public function it_reports_errors_when_the_custom_enum_is_not_setup_correctly()
     {
         try {
             InvalidInstanceField::ONE();
             $this->fail('A custom Enum with an instance field "enums" instead of a static field "enums" should throw a meaningful Exception');
         } catch (\Exception $e) {
             // success
         }

         try {
             InvalidInstanceMethod::ONE();
             $this->fail('A custom Enum with an instance method "enums" instead of a static method "enums" should throw a meaningful Exception');
         } catch (\Exception $e) {
             // success
         }

         try {
             InvalidMissingFieldAndMethod::ONE();
             $this->fail('A custom Enum without a static field or a static method "enums" should throw a meaningful Exception');
         } catch (\Exception $e) {
             // success
         }
     }

     /** @test */
     public function it_lists_all_available_instances()
     {
         $expected = [Salutation::MR(), Salutation::MRS(), Salutation::MS()];
         $actual = Salutation::all();

        // order shall not matter:
        sort($expected);
         sort($actual);

         $this->assertEquals($expected, $actual);
     }

     /** @test */
     public function it_lists_all_available_values()
     {
         $expected = [Salutation::MR()->value(), Salutation::MRS()->value(), Salutation::MS()->value()];
         $actual = Salutation::values();

        // order shall not matter:
        sort($expected);
         sort($actual);

         $this->assertEquals($expected, $actual);
     }

     /** @test */
     public function it_lists_all_available_names()
     {
         $expected = [Salutation::MR()->name(), Salutation::MRS()->name(), Salutation::MS()->name()];
         $actual = Salutation::names();

        // order shall not matter:
        sort($expected);
         sort($actual);

         $this->assertEquals($expected, $actual);
     }

     /** @test */
     public function it_checks_that_the_configured_names_and_values_are_unique()
     {
         $this->markTestSkipped('It currently doesn\'t check for unique names or values. At the moment, this is the responsibility of the developer of the custom enum.');
     }

     /** @test */
     public function it_will_initialize_when_calling_all()
     {
         $this->assertFalse(Color::isInitialized());
         $colors = Color::all();
         $this->assertCount(2, $colors);
     }

     /** @test */
     public function it_can_be_dynamically_initialized_using_the_key_as_a_string()
     {
         $this->assertEquals(Salutation::MR(), Salutation::fromKey('MR'));
     }

    /** @test */
    public function it_returns_null_when_the_requested_key_doesnt_match_the_constraints()
    {
        $this->assertNull(Salutation::fromKey('NONEXISTING'));
    }
}
