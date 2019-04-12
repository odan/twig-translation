<?php

namespace Odan\Test;

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
     * @covers ::__construct
     */
    public function testInstance()
    {
        $this->assertInstanceOf(TwigCompiler::class, $this->newInstance());
    }

    /**
     * Test.
     *
     * @return void
     * @covers ::__construct
     * @expectedException \Exception
     */
    public function testInstanceWithError()
    {
        new TwigCompiler($this->env, '');
    }

    /**
     * Test.
     *
     * @return void
     * @covers ::compile
     * @covers ::compileFiles
     * @covers ::removeDirectory
     */
    public function testCompile()
    {
        $compiler = $this->newInstance();
        $this->assertTrue($compiler->compile());
    }
}
