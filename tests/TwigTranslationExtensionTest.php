<?php

namespace Odan\Test;

use Odan\Twig\TwigTranslationExtension;

/**
 * AssetCacheTest
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
}
