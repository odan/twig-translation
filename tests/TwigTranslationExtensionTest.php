<?php

namespace Odan\Twig\Test;

use Odan\Twig\TwigTranslationExtension;

/**
 * AssetCacheTest.
 *
 * @coversDefaultClass \Odan\Twig\TwigTranslationExtension
 */
class TwigTranslationExtensionTest extends AbstractTest
{
    /**
     * Test create object.
     *
     * @return void
     */
    public function testInstance()
    {
        $extension = $this->newExtensionInstance();
        $this->assertInstanceOf(TwigTranslationExtension::class, $extension);
    }

    /**
     * Test.
     *
     * @covers ::getFunctions
     */
    public function testFunctions(): void
    {
        $extension = $this->newExtensionInstance();
        $this->assertNotEmpty($extension->getFunctions());
    }

    /**
     * Test.
     *
     * @return void
     * @covers ::getFilters
     */
    public function testFilters()
    {
        $extension = $this->newExtensionInstance();
        $this->assertNotEmpty($extension->getFilters());
    }

    /**
     * Test.
     *
     * @covers ::translate
     */
    public function testTranslate(): void
    {
        $extension = $this->newExtensionInstance();
        $this->assertSame('a', $extension->__('a'));
    }

    /**
     * Test.
     *
     * @covers ::getTokenParsers
     */
    public function testGetTokenParsers(): void
    {
        $extension = $this->newExtensionInstance();
        $this->assertEmpty($extension->getTokenParsers());
    }

    /**
     * Test.
     *
     * @covers ::getNodeVisitors
     */
    public function testGetNodeVisitors(): void
    {
        $extension = $this->newExtensionInstance();
        $this->assertEmpty($extension->getNodeVisitors());
    }

    /**
     * Test.
     *
     * @covers ::getTests
     */
    public function testGetTests(): void
    {
        $extension = $this->newExtensionInstance();
        $this->assertEmpty($extension->getTests());
    }

    /**
     * Test.
     *
     * @covers ::getOperators
     */
    public function testGetOperators(): void
    {
        $extension = $this->newExtensionInstance();
        $this->assertEmpty($extension->getOperators());
    }
}
