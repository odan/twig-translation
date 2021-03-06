<?php

namespace Odan\Twig\Test;

use Odan\Twig\TwigTranslationExtension;
use org\bovigo\vfs\vfsStream;
use org\bovigo\vfs\vfsStreamDirectory;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Translation\Formatter\MessageFormatter;
use Symfony\Component\Translation\IdentityTranslator;
use Symfony\Component\Translation\Loader\MoFileLoader;
use Symfony\Component\Translation\Translator;
use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;

/**
 * Base class.
 */
abstract class AbstractTest extends TestCase
{
    /**
     * @var FilesystemLoader
     */
    protected $loader;

    /**
     * @var Environment
     */
    protected $env;

    /**
     * @var TwigTranslationExtension
     */
    protected $extension;

    /**
     * @var vfsStreamDirectory
     */
    protected $root;

    /**
     * @var mixed[]
     */
    protected $options = [];

    /**
     * Set up.
     */
    public function setUp(): void
    {
        $this->options = [
            // Public assets cache directory
            'path' => vfsStream::url('root/public/cache'),
            // Cache settings
            'cache_enabled' => true,
            'cache_path' => vfsStream::url('root/tmp'),
            'cache_name' => 'assets-cache',
            'cache_lifetime' => 0,
            'minify' => true,
        ];

        $this->root = vfsStream::setup('root');
        vfsStream::newDirectory('tmp/twig-cache')->at($this->root);
        vfsStream::newDirectory('tmp/twig-cache/aa')->at($this->root);
        vfsStream::newDirectory('tmp/twig-cache/aa/bb')->at($this->root);
        vfsStream::newDirectory('public')->at($this->root);
        vfsStream::newDirectory('public/cache')->at($this->root);
        vfsStream::newDirectory('templates')->at($this->root);
        vfsStream::newDirectory('templates/sub1')->at($this->root);
        vfsStream::newDirectory('templates/sub1/sub2')->at($this->root);

        $templatePath = vfsStream::url('root/templates');
        $this->loader = new FilesystemLoader([$templatePath]);

        // Add alias path: @public/ -> root/public
        $this->loader->addPath(vfsStream::url('root/public'), 'public');

        // Create cached files
        file_put_contents(vfsStream::url('root/tmp/twig-cache/test1.php'), '<?php echo "test"');
        file_put_contents(vfsStream::url('root/tmp/twig-cache/aa/test2.php'), '<?php echo "test"');
        file_put_contents(vfsStream::url('root/tmp/twig-cache/aa/bb/test3.php'), '<?php echo "test"');
        file_put_contents(vfsStream::url('root/tmp/twig-cache/aa/bb/test4.php'), '<?php echo "test"');

        // Create some twig templates
        file_put_contents(vfsStream::url('root/templates/test.twig'), '{{ test1 }}');
        file_put_contents(vfsStream::url('root/templates/sub1/test2.twig'), '{{ test2 }}');
        file_put_contents(vfsStream::url('root/templates/sub1/sub2/test3.twig'), '{{ test3 }}');

        $options = [
            'path' => $templatePath,
            // Fix vfsStream issue with the tempnam() funtion in the FilesystemCache class
            'cache' => new TwigCacheVfsStream(vfsStream::url('root/tmp/twig-cache')),
        ];

        $this->env = new Environment($this->loader, $options);
        $this->extension = $this->newExtensionInstance();
    }

    /**
     * Create translator.
     *
     * @return TranslatorInterface The translator
     */
    public function createTranslator(): TranslatorInterface
    {
        $translator = new Translator(
            'en_US',
            new MessageFormatter(new IdentityTranslator()),
            null
        );

        $translator->addLoader('mo', new MoFileLoader());

        return $translator;
    }

    /**
     * Create instance.
     *
     * @return TwigTranslationExtension The extension
     */
    public function newExtensionInstance(): TwigTranslationExtension
    {
        return new TwigTranslationExtension($this->createTranslator());
    }
}
