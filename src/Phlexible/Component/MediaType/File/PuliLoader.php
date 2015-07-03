<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Component\MediaType\File;

use Phlexible\Component\MediaType\Compiler\CompilerInterface;
use Phlexible\Component\MediaType\Loader\LoaderInterface;
use Phlexible\Component\MediaType\Model\MediaTypeCollection;
use Puli\Discovery\Api\ResourceDiscovery;
use Symfony\Component\Config\ConfigCache;
use Symfony\Component\Config\Resource\FileResource;

/**
 * Puli media type loader
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class PuliLoader
{
    /**
     * @var ResourceDiscovery
     */
    private $puliDiscovery;

    /**
     * @var LoaderInterface
     */
    private $loader;

    /**
     * @var CompilerInterface
     */
    private $compiler;

    /**
     * @var string
     */
    private $fileDir;

    /**
     * @param ResourceDiscovery $puliDiscovery
     * @param LoaderInterface   $loader
     * @param CompilerInterface $compiler
     * @param string            $fileDir
     * @param string            $cacheDir
     * @param bool              $debug
     */
    public function __construct(
        ResourceDiscovery $puliDiscovery,
        LoaderInterface $loader,
        CompilerInterface $compiler,
        $fileDir,
        $cacheDir,
        $debug
    )
    {
        $this->puliDiscovery = $puliDiscovery;
        $this->loader = $loader;
        $this->compiler = $compiler;
        $this->fileDir = $fileDir;
        $this->cacheDir = $cacheDir;
        $this->debug = $debug;
    }

    /**
     * @return string
     */
    public function getFilename()
    {
        return $this->cacheDir . '/documenttypes' . ($this->debug ? 'Debug' : '') . '.php';
    }

    /**
     * @return MediaTypeCollection
     */
    public function loadMediaTypes()
    {
        $mediaTypes = new MediaTypeCollection();
        $configCache = new ConfigCache($this->getFilename(), $this->debug);

        if (!$configCache->isFresh()) {
            $resources = array();
            $r = new \ReflectionClass($this);
            $resources[] = new FileResource($r->getFileName());
            $r = new \ReflectionClass($this->compiler);
            $resources[] = new FileResource($r->getFileName());
            foreach ($this->puliDiscovery->findByType("phlexible/mediatypes") as $binding) {
                foreach ($binding->getResources() as $resource) {
                    /* @var $resource \Puli\Repository\Resource\FileResource */
                    $file = $resource->getFilesystemPath();
                    $mediaTypes->add($this->loader->load($file));
                    $resources[basename($file)] = new FileResource($file);
                }
            }

            $resources = array_values($resources);

            $configCache->write($this->compiler->compile($mediaTypes), $resources);
        }

        include (string) $configCache;

        $classname = $this->compiler->getClassname();
        $mediaTypes = new $classname();

        return $mediaTypes;
    }
}
