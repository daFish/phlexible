<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Component\MediaTemplate\File;

use JMS\Serializer\Serializer;
use Phlexible\Component\MediaTemplate\Model\TemplateInterface;
use Puli\Discovery\Api\ResourceDiscovery;
use Puli\Repository\Api\EditableRepository;
use Puli\Repository\Resource\FileResource;
use Symfony\Component\Filesystem\Filesystem;

/**
 * Puli template repository.
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
     * @var Serializer
     */
    private $serializer;

    /**
     * @var array
     */
    private $templates;

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
        if ($this->templates === null) {
            $this->templates = array();

            foreach ($this->puliDiscovery->findByType('phlexible/mediatemplates') as $binding) {
                foreach ($binding->getResources() as $resource) {
                    $template = $this->serializer->deserialize(
                        $resource->getBody(),
                        'Phlexible\Component\MediaTemplate\Domain\AbstractTemplate',
                        $this->defaultDumpType
                    );
                    $this->templates[$template->getKey()] = $template;
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
        $templates = $this->loadAll();

        if (isset($templates[$key])) {
            return $templates[$key];
        }

        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function writeTemplate(TemplateInterface $template, $type = null)
    {
        if (!$type) {
            $type = $this->defaultDumpType;
        }

        $content = $this->serializer->serialize($template, $this->defaultDumpType);

        $filename = strtolower("{$template->getKey()}.{$type}");
        $path = "{$this->dumpDir}/$filename";
        $filesystem = new Filesystem();
        $filesystem->dumpFile($path, $content);

        $resourcePath = "{$this->puliResourceDir}/$filename";
        $resource = new FileResource($path, $resourcePath);
        $this->puliRepository->add($resourcePath, $resource);
    }
}
