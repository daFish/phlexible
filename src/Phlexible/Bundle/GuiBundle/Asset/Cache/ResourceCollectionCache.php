<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Bundle\GuiBundle\Asset\Cache;

use Puli\Discovery\Api\Binding\ResourceBinding;
use Puli\Repository\Resource\FileResource;
use Symfony\Component\Filesystem\Exception\IOException;
use Symfony\Component\Filesystem\Filesystem;

/**
 * Filter phlexible baseurl and basepath
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class ResourceCollectionCache
{
    /**
     * @var string
     */
    private $file;

    /**
     * @var boolean
     */
    private $debug;

    /**
     * @param string $file
     * @param bool   $debug
     */
    public function __construct($file, $debug)
    {
        $this->file = $file;
        $this->debug = $debug;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->file;
    }

    /**
     * @param ResourceBinding[] $bindings
     *
     * @return bool
     */
    public function isFresh(array $bindings)
    {
        if (!file_exists($this->file)) {
            return false;
        } elseif (!$this->debug) {
            return true;
        }

        $timestamp = filemtime($this->file);

        foreach ($bindings as $binding) {
            foreach ($binding->getResources() as $resource) {
                if ($resource instanceof FileResource && $timestamp < $resource->getMetadata()->getModificationTime()) {
                    return false;
                }
            }
        }

        return true;
    }

    /**
     * @param string $content
     */
    public function write($content)
    {
        $mode = 0666;
        $umask = umask();

        $filesystem = new Filesystem();
        $filesystem->dumpFile($this->file, $content, null);

        try {
            $filesystem->chmod($this->file, $mode, $umask);
        } catch (IOException $e) {
            // discard chmod failure (some filesystem may not support it)
        }
    }
}
