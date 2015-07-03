<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Component\MediaTemplate\File;

use Phlexible\Component\MediaTemplate\File\Loader\LoaderInterface;
use Phlexible\Component\MediaTemplate\Model\TemplateCollection;
use Puli\Discovery\Api\ResourceDiscovery;

/**
 * Puli template loader
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class PuliTemplateLoader
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
     * @return TemplateCollection
     */
    public function loadTemplates()
    {
        $templates = new TemplateCollection();

        foreach ($this->puliDiscovery->findByType('phlexible/mediatemplates') as $binding) {
            foreach ($binding->getResources() as $resource) {
                $path = $resource->getFilesystemPath();
                $extension = pathinfo($path, PATHINFO_EXTENSION);
                if (!isset($this->loaders[$extension])) {
                    continue;
                }
                $loader = $this->loaders[$extension];
                $templates->add($loader->load($path));
            }
        }

        return $templates;
    }
}
