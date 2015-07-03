<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Component\MetaSet\File;

use Phlexible\Component\MetaSet\File\Loader\LoaderInterface;
use Phlexible\Component\MetaSet\Model\MetaSetCollection;
use Puli\Discovery\Api\ResourceDiscovery;

/**
 * Puli meta set loader
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class PuliMetaSetLoader
{
    /**
     * @var ResourceDiscovery
     */
    private $puliDiscovery;

    /**
     * @var LoaderInterface[]
     */
    private $loaders = [];

    /**
     * @param ResourceDiscovery $puliDiscovery
     */
    public function __construct(ResourceDiscovery $puliDiscovery)
    {
        $this->puliDiscovery = $puliDiscovery;
    }

    /**
     * @param LoaderInterface $loader
     *
     * @return $this
     */
    public function addLoader(LoaderInterface $loader)
    {
        $this->loaders[$loader->getExtension()] = $loader;

        return $this;
    }

    /**
     * @return MetaSetCollection
     */
    public function loadMetaSets()
    {
        $metaSets = new MetaSetCollection();

        foreach ($this->puliDiscovery->findByType('phlexible/metasets') as $binding) {
            foreach ($binding->getResources() as $resource) {
                $path = $resource->getFilesystemPath();
                $extension = pathinfo($path, PATHINFO_EXTENSION);
                if (!isset($this->loaders[$extension])) {
                    continue;
                }
                $loader = $this->loaders[$extension];
                $metaSets->add($loader->load($path));
            }
        }

        return $metaSets;
    }
}
