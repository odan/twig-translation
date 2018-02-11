<?php

namespace Odan\Test;

use RuntimeException;
use Twig_Cache_Filesystem;

// Shim to not throw "Function opcache_invalidate not found" error when opcache is not enabled
if (!function_exists('\opcache_invalidate')) {
    /**
     * @param string $script
     * @param bool $force
     *
     * @return void
     */
    function opcache_invalidate($script, $force = false)
    {
    }
}

// Shim to not throw "Function apc_compile_file not found" error when opcache is not enabled
if (!function_exists('\apc_compile_file')) {
    /**
     * @param string $filename
     * @param bool $atomic
     *
     * @return void
     */
    function apc_compile_file(string $filename, bool $atomic = true)
    {
    }
}

/**
 * Class TwigCacheVfs
 */
class TwigCacheVfsStream extends Twig_Cache_Filesystem
{
    private $options;

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
        $tmpFile = $dir . '/' . uniqid(mt_rand(), true) . '_' . basename($key2);

        if (false !== @file_put_contents($tmpFile, $content) && @rename($tmpFile, $key)) {
            @chmod($key, 0666 & ~umask());

            if (self::FORCE_BYTECODE_INVALIDATION == ($this->options & self::FORCE_BYTECODE_INVALIDATION)) {
                // Compile cached file into bytecode cache
                if (function_exists('opcache_invalidate')) {
                    opcache_invalidate($key, true);
                } elseif (function_exists('apc_compile_file')) {
                    apc_compile_file($key);
                }
            }

            return;
        }

        throw new RuntimeException(sprintf('Failed to write cache file "%s".', $key));
    }
}
