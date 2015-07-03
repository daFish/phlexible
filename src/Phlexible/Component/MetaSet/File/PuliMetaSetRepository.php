<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Component\MetaSet\File;

use Phlexible\Component\MetaSet\File\Dumper\DumperInterface;
use Phlexible\Component\MetaSet\File\Parser\ParserInterface;
use Phlexible\Component\MetaSet\Model\MetaSetCollection;
use Phlexible\Component\MetaSet\Model\MetaSetInterface;
use Puli\Discovery\Api\ResourceDiscovery;
use Puli\Manager\Api\Puli;
use Puli\Repository\Api\EditableRepository;
use Symfony\Component\Filesystem\Filesystem;

/**
 * Puli meta set repository
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class PuliMetaSetRepository implements MetaSetRepositoryInterface
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
    private $dumpDir;

    /**
     * @var string
     */
    private $defaultDumpType;

    /**
     * @var ParserInterface[]
     */
    private $parsers = [];

    /**
     * @var DumperInterface[]
     */
    private $dumpers = [];

    /**
     * @param ResourceDiscovery  $puliDiscovery
     * @param EditableRepository $puliRepository
     * @param string             $defaultDumpType
     * @param string             $dumpDir
     */
    public function __construct(ResourceDiscovery $puliDiscovery, EditableRepository $puliRepository, $defaultDumpType, $dumpDir)
    {
        $this->puliDiscovery = $puliDiscovery;
        $this->puliRepository = $puliRepository;
        $this->defaultDumpType = $defaultDumpType;
        $this->dumpDir = $dumpDir;
    }

    /**
     * @param string          $type
     * @param ParserInterface $parser
     *
     * @return $this
     */
    public function addParser($type, ParserInterface $parser)
    {
        $this->parsers[$type] = $parser;

        return $this;
    }

    /**
     * @param string          $type
     * @param DumperInterface $dumper
     *
     * @return $this
     */
    public function addDumper($type, DumperInterface $dumper)
    {
        $this->dumpers[$type] = $dumper;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function loadMetaSets()
    {
        $metaSets = new MetaSetCollection();

        foreach ($this->puliDiscovery->findByType('phlexible/metasets') as $binding) {
            foreach ($binding->getResources() as $resource) {
                $extension = pathinfo($resource->getFilesystemPath(), PATHINFO_EXTENSION);
                if (!isset($this->parsers[$extension])) {
                    continue;
                }
                $parser = $this->parsers[$extension];
                $metaSets->add($parser->parser($resource->getBody()));
            }
        }

        return $metaSets;
    }

    /**
     * {@inheritdoc}
     */
    public function dumpMetaSet(MetaSetInterface $metaSet, $type = null)
    {
        if (!$type) {
            $type = $this->defaultDumpType;
        }

        $dumper = $this->dumpers[$type];
        $filename = strtolower("{$this->dumpDir}/{$metaSet->getId()}.{$type}");
        $content = $dumper->dump($metaSet);

        $filesystem = new Filesystem();
        $filesystem->dumpFile($filename, $content);

        $rootDir = '/Users/swentz/Sites/phlexible-tipfinder/tipfinder-refactorings/.puli';
        $puli = new Puli($rootDir);
        $puli->start();
        $repoManager = $puli->getRepositoryManager();

        $repoManager->clearRepository();
        $repoManager->buildRepository();
    }
}
