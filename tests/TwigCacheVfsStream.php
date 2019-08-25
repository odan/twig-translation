<?php

namespace Odan\Test;

use RuntimeException;
use Twig\Cache\FilesystemCache;

/**
 * Class.
 */
class TwigCacheVfsStream extends FilesystemCache
{
    /**
     * Write.
     *
     * @param string $key
     * @param string $content
     */
    public function write($key, $content)
    {
        $dir = dirname($key);
        if (!is_dir($dir)) {
            if (false === @mkdir($dir, 0777, true)) {
                clearstatcache(true, $dir);
                if (!is_dir($dir)) {
                    throw new RuntimeException(sprintf('Unable to create the cache directory (%s).', $dir));
                }
            }
        } elseif (!is_writable($dir)) {
            throw new RuntimeException(sprintf('Unable to write in the cache directory (%s).', $dir));
        }

        // Fixed tempnam() issue with VfS
        $key2 = str_replace('vfs:/', '', $key);
        $tmpFile = $dir . '/' . uniqid((string)mt_rand(), true) . '_' . basename($key2);

        if (false !== @file_put_contents($tmpFile, $content) && @rename($tmpFile, $key)) {
            @chmod($key, 0666 & ~umask());

            return;
        }

        throw new RuntimeException(sprintf('Failed to write cache file "%s".', $key));
    }
}
