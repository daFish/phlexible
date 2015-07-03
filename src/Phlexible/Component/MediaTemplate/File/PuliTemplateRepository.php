<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Component\MediaTemplate\File;

use Phlexible\Component\MediaTemplate\File\Dumper\DumperInterface;
use Phlexible\Component\MediaTemplate\File\Parser\ParserInterface;
use Phlexible\Component\MediaTemplate\Model\TemplateCollection;
use Phlexible\Component\MediaTemplate\Model\TemplateInterface;
use Puli\Discovery\Api\ResourceDiscovery;
use Puli\Manager\Api\Puli;
use Puli\Manager\Api\Repository\RepositoryManager;
use Puli\Repository\Api\EditableRepository;

/**
 * Puli template repository
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class PuliTemplateRepository implements TemplateRepositoryInterface
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
    public function loadTemplates()
    {
        $templates = new TemplateCollection();

        foreach ($this->puliDiscovery->findByType('phlexible/mediatemplates') as $binding) {
            foreach ($binding->getResources() as $resource) {
                $extension = pathinfo($resource->getPath(), PATHINFO_EXTENSION);
                if (!isset($this->parsers[$extension])) {
                    continue;
                }
                $parser = $this->parsers[$extension];
                $templates->add($parser->parse($resource->getBody()));
            }
        }

        return $templates;
    }

    /**
     * {@inheritdoc}
     */
    public function dumpTemplate(TemplateInterface $template, $type = null)
    {
        if (!$type) {
            $type = $this->defaultDumpType;
        }
        $dumper = $this->dumpers[$type];
        $filename = strtolower("{$this->dumpDir}/{$template->getKey()}.{$type}");
        $dumper->dump($filename, $template);

        $rootDir = '/Users/swentz/Sites/phlexible-tipfinder/tipfinder-refactorings/.puli';
        $puli = new Puli($rootDir);
        $puli->start();
        $repoManager = $puli->getRepositoryManager();

        $repoManager->clearRepository();
        $repoManager->buildRepository();
    }
}