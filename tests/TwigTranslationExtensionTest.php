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
     * Test create object.
     *
     * @expectedException \InvalidArgumentException
     *
     * @return void
     */
    public function testInstanceError()
    {
        new TwigTranslationExtension();
    }

    /**
     * Test create object.
     *
     * @expectedException \InvalidArgumentException
     *
     * @return void
     */
    public function testInstanceError2()
    {
        new TwigTranslationExtension('__non_existing_function__');
    }

    /**
     * Test.
     *
     * @return void
     * @covers ::getFunctions
     */
    public function testFunctions()
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
     * @return void
     * @covers ::__
     */
    public function testTranslate()
    {
        $extension = $this->newExtensionInstance();
        $this->assertSame('a', $extension->__('a'));
    }

    /**
     * Test.
     *
     * @return void
     * @covers ::getTokenParsers
     */
    public function testGetTokenParsers()
    {
        $extension = $this->newExtensionInstance();
        $this->assertEmpty($extension->getTokenParsers());
    }

    /**
     * Test.
     *
     * @return void
     * @covers ::getNodeVisitors
     */
    public function testGetNodeVisitors()
    {
        $extension = $this->newExtensionInstance();
        $this->assertEmpty($extension->getNodeVisitors());
    }

    /**
     * Test.
     *
     * @return void
     * @covers ::getTests
     */
    public function testGetTests()
    {
        $extension = $this->newExtensionInstance();
        $this->assertEmpty($extension->getTests());
    }

    /**
     * Test.
     *
     * @return void
     * @covers ::getOperators
     */
    public function testGetOperators()
    {
        $extension = $this->newExtensionInstance();
        $this->assertEmpty($extension->getOperators());
    }
}
