<?php

namespace Odan\Test;

use Odan\Twig\TwigTranslationExtension;
use PHPUnit\Framework\TestCase;

/**
 * BaseTest
 */
abstract class AbstractTest extends TestCase
{
    /**
     * @var TwigTranslationExtension
     */
    protected $extension;

    /**
     * Set up
     */
    public function setUp()
    {
        $this->extension = $this->newExtensionInstance();
    }

    /**
     * @return TwigTranslationExtension
     */
    public function newExtensionInstance()
    {
        return new TwigTranslationExtension();
    }
}
