<?php

namespace Odan\Twig\Test;

use Odan\Twig\TwigCompiler;
use org\bovigo\vfs\vfsStream;

/**
 * AssetCacheTest.
 *
 * @coversDefaultClass \Odan\Twig\TwigCompiler
 */
class TwigCompilerTest extends AbstractTest
{
    /**
     * @return TwigCompiler
     */
    public function newInstance()
    {
        return new TwigCompiler($this->env, vfsStream::url('root/tmp/twig-cache'));
    }

    /**
     * Test.
     *
     * @return void
     */
    public function testInstance()
    {
        $this->assertInstanceOf(TwigCompiler::class, $this->newInstance());
    }

    /**
     * Test.
     *
     * @return void
     */
    public function testInstanceWithError()
    {
        $this->expectException(\Exception::class);
        new TwigCompiler($this->env, '');
    }

    /**
     * Test.
     *
     * @return void
     */
    public function testCompile()
    {
        $compiler = $this->newInstance();
        $this->assertTrue($compiler->compile());
    }
}
