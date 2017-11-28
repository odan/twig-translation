<?php

namespace Odan\Twig;

use DirectoryIterator;
use Exception;
use FilesystemIterator;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use SplFileInfo;
use Twig_Environment;
use Twig_Loader_Filesystem;

/**
 * Class TwigCompiler
 */
class TwigCompiler
{
    /**
     * @var Twig_Environment
     */
    private $twig;

    /**
     * @var string
     */
    private $cachePath;

    /**
     * The constructor.
     *
     * @param Twig_Environment $twig The Twig Environment instance
     * @param string $cachePath The twig cache path
     * @throws Exception
     */
    public function __construct(Twig_Environment $twig, string $cachePath)
    {
        if (empty($cachePath)) {
            throw new Exception('The cache path cannot be empty');
        }

        $this->twig = $twig;
        $this->cachePath = str_replace('\\', '/', trim($cachePath, '\/'));
    }

    /**
     * Compile all twig templates.
     *
     * @return bool Success
     * @throws Exception Exception
     */
    public function compile()
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

        if (!$this->twig->getCache()) {
            throw new Exception('The Twig cache must be enabled!');
        }

        /* @var Twig_Loader_Filesystem $loader */
        $loader = $this->twig->getLoader();
        $paths = $loader->getPaths();

        foreach ($paths as $path) {
            $this->compileFiles($path);
        }

        return true;
    }

    /**
     * Compile Twig files.
     *
     * @param string $viewPath
     */
    private function compileFiles($viewPath)
    {
        $directory = new RecursiveDirectoryIterator($viewPath, FilesystemIterator::SKIP_DOTS);
        foreach (new RecursiveIteratorIterator($directory, RecursiveIteratorIterator::SELF_FIRST) as $file) {
            /* @var SplFileInfo $file */
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
     * @return bool True on success or false on failure.
     */
    private function removeDirectory($path)
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

        /* @var SplFileInfo $file */
        foreach ($files as $file) {
            $fileName = $file->getPathname();
            unlink($fileName);
        }

        return rmdir($path);
    }
}
