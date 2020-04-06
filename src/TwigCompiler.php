<?php

namespace Odan\Twig;

use DirectoryIterator;
use FilesystemIterator;
use InvalidArgumentException;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use SplFileInfo;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;

/**
 * Twig file compiler.
 */
final class TwigCompiler
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
     * @var bool
     */
    private $verbose;

    /**
     * The constructor.
     *
     * @param Environment $twig The Twig Environment instance
     * @param string $cachePath The twig cache path
     * @param bool $verbose Enable verbose output
     */
    public function __construct(Environment $twig, string $cachePath, bool $verbose = false)
    {
        if (empty($cachePath)) {
            throw new InvalidArgumentException('The cache path is required');
        }

        $this->twig = $twig;
        $this->cachePath = str_replace('\\', '/', $cachePath);
        $this->verbose = $verbose;
    }

    /**
     * Compile all twig templates.
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
        if (!$this->twig->getCache()) {
            $this->twig->setCache($this->cachePath);
        }

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
     * @param string $viewPath The template path
     *
     * @return void
     */
    private function compileFiles(string $viewPath)
    {
        $directory = new RecursiveDirectoryIterator($viewPath, FilesystemIterator::SKIP_DOTS);

        foreach (new RecursiveIteratorIterator($directory, RecursiveIteratorIterator::SELF_FIRST) as $file) {
            /** @var SplFileInfo $file */
            if (!$file->isFile() || $file->getExtension() !== 'twig') {
                continue;
            }

            $templateName = substr($file->getPathname(), strlen($viewPath) + 1);
            $templateName = str_replace('\\', '/', $templateName);

            if ($this->verbose) {
                echo sprintf("Parsing: %s\n", $templateName);
            }

            $className = $this->twig->getTemplateClass($templateName);

            $this->twig->loadTemplate($className, $templateName);
        }
    }

    /**
     * Remove directory recursively.
     * This function is compatible with vfsStream.
     *
     * @param string $path The path
     *
     * @return bool True on success or false on failure
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
