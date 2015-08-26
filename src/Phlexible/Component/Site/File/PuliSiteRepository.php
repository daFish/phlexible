<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Component\Site\File;

use FluentDOM\Document;
use Phlexible\Component\Site\Domain\Site;
use Phlexible\Component\Site\File\Dumper\XmlDumper;
use Phlexible\Component\Site\File\Parser\XmlParser;
use Puli\Discovery\Api\ResourceDiscovery;
use Puli\Repository\Api\EditableRepository;
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
     * @var XmlParser
     */
    private $parser;

    /**
     * @var XmlDumper
     */
    private $dumper;

    /**
     * @param ResourceDiscovery  $puliDiscovery
     * @param EditableRepository $puliRepository
     * @param string             $defaultDumpType
     * @param string             $dumpDir
     * @param string             $puliResourceDir
     */
    public function __construct(
        ResourceDiscovery $puliDiscovery,
        EditableRepository $puliRepository,
        $defaultDumpType,
        $dumpDir,
        $puliResourceDir
    ) {
        $this->puliDiscovery = $puliDiscovery;
        $this->puliRepository = $puliRepository;
        $this->defaultDumpType = $defaultDumpType;
        $this->dumpDir = $dumpDir;
        $this->puliResourceDir = $puliResourceDir;

        $this->parser = new XmlParser($this);
        $this->dumper = new XmlDumper();
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
                    $sites[$id] = $this->parse($this->loadDomFromResource($resource));
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
        return $this->parse($this->loadSiteDom($siteId));
    }

    /**
     * @param string $siteId
     *
     * @return Document
     * @throws \Exception
     */
    private function loadSiteDom($siteId)
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
            throw new \Exception("Resource for $siteId not found.");
        }

        return $this->loadDomFromResource($foundResource);
    }

    /**
     * {@inheritdoc}
     */
    public function write(Site $site)
    {
        $content = $this->dumper->dump($site);

        $filename = strtolower("{$site->getId()}.xml");
        $path = "{$this->dumpDir}/$filename";

        $filesystem = new Filesystem();
        $filesystem->dumpFile($path, $content);

        $resourcePath = "{$this->puliResourceDir}/$filename";

        $resource = new FileResource($path, $resourcePath);
        $this->puliRepository->add($resourcePath, $resource);
    }

    /**
     * @param FileResource $resource
     *
     * @return Document
     */
    private function loadDomFromResource(FileResource $resource)
    {
        $dom = new Document();
        $dom->formatOutput = true;
        $dom->load($resource->getFilesystemPath());

        return $dom;
    }

    /**
     * @param Document $dom
     *
     * @return Site
     */
    private function parse(Document $dom)
    {
        return $this->parser->parse($dom);
    }
}
