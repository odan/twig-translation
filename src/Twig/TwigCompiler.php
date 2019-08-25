<?php

namespace Odan\Twig;

use DirectoryIterator;
use Exception;
use FilesystemIterator;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use RuntimeException;
use SplFileInfo;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;

/**
 * Class TwigCompiler.
 */
class TwigCompiler
{
    /**
     * @var Environment
     */
    private $twig;

    /**
     * @var string
     */
    private $cachePath;

    /**
     * The constructor.
     *
     * @param Environment $twig The Twig Environment instance
     * @param string $cachePath The twig cache path
     *
     * @throws Exception
     */
    public function __construct(Environment $twig, string $cachePath)
    {
        if (empty($cachePath)) {
            throw new RuntimeException('The cache path cannot be empty');
        }

        $this->twig = $twig;
        $this->cachePath = str_replace('\\', '/', trim($cachePath, '\/'));
    }

    /**
     * Compile all twig templates.
     *
     * @throws Exception Exception
     *
     * @return bool Success
     */
    public function compile(): bool
    {
        // Delete old twig cache files
        if (file_exists($this->cachePath)) {
            $this->removeDirectory($this->cachePath);
        }

        if (!file_exists($this->cachePath)) {
            mkdir($this->cachePath);
        }

        // Iterate over all your templates and force compilation
        $this->twig->disableDebug();
        $this->twig->enableAutoReload();

        // The Twig cache must be enabled
        $this->twig->setCache($this->cachePath);

        $loader = $this->twig->getLoader();

        if ($loader instanceof FilesystemLoader) {
            $paths = $loader->getPaths();

            foreach ($paths as $path) {
                $this->compileFiles($path);
            }
        }

        return true;
    }

    /**
     * Compile Twig files.
     *
     * @param string $viewPath The templates path
     *
     * @return void
     */
    private function compileFiles(string $viewPath)
    {
        $directory = new RecursiveDirectoryIterator($viewPath, FilesystemIterator::SKIP_DOTS);

        foreach (new RecursiveIteratorIterator($directory, RecursiveIteratorIterator::SELF_FIRST) as $file) {
            /** @var SplFileInfo $file */
            if ($file->isFile() && $file->getExtension() === 'twig') {
                $templateName = substr($file->getPathname(), strlen($viewPath) + 1);
                $templateName = str_replace('\\', '/', $templateName);
                //echo sprintf("Parsing: %s\n", $templateName);
                $this->twig->loadTemplate($templateName);
            }
        }
    }

    /**
     * Remove directory recursively.
     * This function is compatible with vfsStream.
     *
     * @param string $path Path
     *
     * @return bool true on success or false on failure
     */
    private function removeDirectory(string $path): bool
    {
        $iterator = new DirectoryIterator($path);
        foreach ($iterator as $fileInfo) {
            if ($fileInfo->isDot() || !$fileInfo->isDir()) {
                continue;
            }
            $dirName = $fileInfo->getPathname();
            $this->removeDirectory($dirName);
        }

        $files = new FilesystemIterator($path);

        /** @var SplFileInfo $file */
        foreach ($files as $file) {
            $fileName = $file->getPathname();
            unlink($fileName);
        }

        return rmdir($path);
    }
}
