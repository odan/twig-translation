<?php

namespace Odan\Twig\Test;

/**
 * Test.
 */
class TranslateFunctionTest extends AbstractTest
{
    /**
     * Test.
     */
    public function testTranslate(): void
    {
        __test($this->createTranslator());

        $this->assertSame('Test', __test('Test'));
        $this->assertSame('Test 1', __test('Test %s', 1));
        $this->assertSame('Test example 1', __test('Test %s %s', 'example', 1));
    }
}
