<?php

namespace Tests\Unit;

use Tests\TestCase;

class SimpleTest extends TestCase
{
    /** @test */
    public function basic_test_passes()
    {
        $this->assertTrue(true);
    }

    /** @test */
    public function basic_math_works()
    {
        $this->assertEquals(4, 2 + 2);
    }

    /** @test */
    public function string_operations_work()
    {
        $text = "Hello World";
        $this->assertStringContainsString("Hello", $text);
        $this->assertStringContainsString("World", $text);
    }
}
