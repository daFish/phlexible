<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Component\Site\File;

use JMS\Serializer\Serializer;
use Phlexible\Component\Site\Domain\Site;
use Puli\Discovery\Api\ResourceDiscovery;
use Puli\Repository\Api\EditableRepository;
use Puli\Repository\Api\Resource\BodyResource;
use Puli\Repository\Resource\FileResource;
use Symfony\Component\Filesystem\Filesystem;

/**
 * Puli site repository
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class PuliSiteRepository implements SiteRepositoryInterface
{
    /**
     * @var ResourceDiscovery
     */
    private $puliDiscovery;

    /**
     * @var EditableRepository
     */
    private $puliRepository;

    /**
     * @var Serializer
     */
    private $serializer;

    /**
     * @var string
     */
    private $defaultDumpType;

    /**
     * @var string
     */
    private $dumpDir;

    /**
     * @var string
     */
    private $puliResourceDir;

    /**
     * @param ResourceDiscovery  $puliDiscovery
     * @param EditableRepository $puliRepository
     * @param Serializer         $serializer
     * @param string             $defaultDumpType
     * @param string             $dumpDir
     * @param string             $puliResourceDir
     */
    public function __construct(
        ResourceDiscovery $puliDiscovery,
        EditableRepository $puliRepository,
        Serializer $serializer,
        $defaultDumpType,
        $dumpDir,
        $puliResourceDir
    ) {
        $this->puliDiscovery = $puliDiscovery;
        $this->puliRepository = $puliRepository;
        $this->serializer = $serializer;
        $this->defaultDumpType = $defaultDumpType;
        $this->dumpDir = $dumpDir;
        $this->puliResourceDir = $puliResourceDir;
    }

    /**
     * {@inheritdoc}
     */
    public function loadAll()
    {
        $sites = array();
        foreach ($this->puliDiscovery->findByType('phlexible/sites') as $bindings) {
            foreach ($bindings->getResources() as $resource) {
                $id = basename($resource->getPath(), '.xml');
                if (!isset($files[$id])) {
                    $sites[$id] = $this->deserialize($resource->getBody());
                }
            }
        }

        return array_values($sites);
    }

    /**
     * {@inheritdoc}
     */
    public function load($siteId)
    {
        return $this->deserialize($this->findResource($siteId)->getBody());
    }

    /**
     * @param string $content
     *
     * @return Site
     */
    private function deserialize($content)
    {
        return $this->serializer->deserialize($content, 'Phlexible\Component\Site\Domain\Site', $this->defaultDumpType);
    }

    /**
     * @param string $siteId
     *
     * @return BodyResource
     * @throws \Exception
     */
    private function findResource($siteId)
    {
        $foundResource = null;
        foreach ($this->puliDiscovery->findByType('phlexible/sites') as $bindings) {
            foreach ($bindings->getResources() as $resource) {
                $id = basename($resource->getPath(), '.xml');
                if ($siteId === $id) {
                    $foundResource = $resource;
                    break 2;
                }
            }
        }

        if (!$foundResource) {
            throw new \Exception("Resource for site $siteId not found.");
        }

        return $foundResource;
    }

    /**
     * {@inheritdoc}
     */
    public function write(Site $site)
    {
        $content = $this->serializer->serialize($site, $this->defaultDumpType);

        $filename = strtolower("{$site->getId()}.xml");
        $path = "{$this->dumpDir}/$filename";

        $filesystem = new Filesystem();
        $filesystem->dumpFile($path, $content);

        $resourcePath = "{$this->puliResourceDir}/$filename";

        $resource = new FileResource($path, $resourcePath);
        $this->puliRepository->add($resourcePath, $resource);
    }
}
