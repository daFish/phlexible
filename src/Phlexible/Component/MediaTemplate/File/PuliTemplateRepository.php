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
use Puli\Repository\Api\EditableRepository;
use Puli\Repository\Resource\FileResource;
use Symfony\Component\Filesystem\Filesystem;

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
     * @var ParserInterface[]
     */
    private $parsers = [];

    /**
     * @var DumperInterface[]
     */
    private $dumpers = [];

    /**
     * @var TemplateCollection
     */
    private $templates;

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
    public function loadAll()
    {
        if ($this->templates === null) {
            $this->templates = new TemplateCollection();

            foreach ($this->puliDiscovery->findByType('phlexible/mediatemplates') as $binding) {
                foreach ($binding->getResources() as $resource) {
                    $extension = pathinfo($resource->getPath(), PATHINFO_EXTENSION);
                    if (!isset($this->parsers[$extension])) {
                        continue;
                    }
                    $parser = $this->parsers[$extension];
                    $this->templates->add($parser->parse($resource->getBody()));
                }
            }
        }

        return $this->templates;
    }

    /**
     * {@inheritdoc}
     */
    public function load($key)
    {
        return $this->loadAll()->get($key);
    }

    /**
     * {@inheritdoc}
     */
    public function writeTemplate(TemplateInterface $template, $type = null)
    {
        if (!$type) {
            $type = $this->defaultDumpType;
        }

        $dumper = $this->dumpers[$type];
        $content = $dumper->dump($template);

        $filename = strtolower("{$template->getKey()}.{$type}");
        $path = "{$this->dumpDir}/$filename";
        $filesystem = new Filesystem();
        $filesystem->dumpFile($path, $content);

        $resourcePath = "{$this->puliResourceDir}/$filename";
        $resource = new FileResource($path, $resourcePath);
        $this->puliRepository->add($resourcePath, $resource);
    }
}
