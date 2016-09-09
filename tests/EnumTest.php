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

use macrominds\enums\Salutation;

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

     /** @test */
     public function it_cannot_be_instanciated_directly()
     {
         $this->expectException(\Error::class);
         new Salutation(4);
     }
}
