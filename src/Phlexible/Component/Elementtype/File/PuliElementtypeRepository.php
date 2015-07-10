<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Component\Elementtype\File;

use FluentDOM\Document;
use Phlexible\Component\Elementtype\File\Dumper\XmlDumper;
use Phlexible\Component\Elementtype\File\Parser\XmlParser;
use Phlexible\Component\Elementtype\Model\Elementtype;
use Puli\Discovery\Api\ResourceDiscovery;
use Puli\Repository\Api\EditableRepository;
use Puli\Repository\Resource\FileResource;
use Symfony\Component\Filesystem\Filesystem;

/**
 * XML loader
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class PuliElementtypeRepository implements ElementtypeRepositoryInterface
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
        $elementtypes = array();
        foreach ($this->puliDiscovery->findByType('phlexible/elementtypes') as $bindings) {
            foreach ($bindings->getResources() as $resource) {
                $id = basename($resource->getPath(), '.xml');
                if (!isset($files[$id])) {
                    $elementtypes[$id] = $this->parse($this->loadDomFromResource($resource));
                }
            }
        }

        return array_values($elementtypes);
    }

    /**
     * {@inheritdoc}
     */
    public function load($elementtypeId)
    {
        return $this->parse($this->loadElementtypeDom($elementtypeId));
    }

    /**
     * @param string $elementtypeId
     *
     * @return Document
     * @throws \Exception
     */
    private function loadElementtypeDom($elementtypeId)
    {
        $foundResource = null;
        foreach ($this->puliDiscovery->findByType('phlexible/elementtypes') as $bindings) {
            foreach ($bindings->getResources() as $resource) {
                $id = basename($resource->getPath(), '.xml');
                if ($elementtypeId === $id) {
                    $foundResource = $resource;
                    break 2;
                }
            }
        }

        if (!$foundResource) {
            throw new \Exception();
        }

        return $this->loadDomFromResource($foundResource);
    }

    /**
     * {@inheritdoc}
     */
    public function write(Elementtype $elementtype)
    {
        $content = $this->dumper->dump($elementtype);

        $filename = strtolower("{$elementtype->getId()}..xml");
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

        $this->applyReferenceElementtype($dom);

        return $dom;
    }

    /**
     * @param Document $dom
     */
    private function applyReferenceElementtype(Document $dom)
    {
        foreach ($dom->xpath()->evaluate('//node[@referenceElementtypeId]') as $node) {
            /* @var $node \FluentDOM\Element */
            $referenceElementtypeId = $node->getAttribute('referenceElementtypeId');
            $referenceDom = $this->loadElementtypeDom($referenceElementtypeId);
            foreach ($referenceDom->documentElement->evaluate('structure[1]/node') as $referenceNode) {
                $node->appendElement('references')->append($referenceNode);
            }
        }
    }

    /**
     * @param Document $dom
     *
     * @return Elementtype
     */
    private function parse(Document $dom)
    {
        return $this->parser->parse($dom);
    }
}
