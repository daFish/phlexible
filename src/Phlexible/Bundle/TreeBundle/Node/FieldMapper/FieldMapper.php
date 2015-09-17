<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Bundle\TreeBundle\Node\FieldMapper;

use Phlexible\Bundle\TreeBundle\Node\NodeContext;

/**
 * Field mapper.
 *
 * @author Stephan Wentz <swentz@brainbits.net>
 */
class FieldMapper
{
    /**
     * @var ValueExtractorInterface
     */
    private $valueExtractor;

    /**
     * @var array
     */
    private $availableLanguages;

    /**
     * @var FieldMapperInterface[]
     */
    private $mappers = array();

    /**
     * @param ValueExtractorInterface $valueExtractor
     * @param string                  $availableLanguages
     * @param FieldMapperInterface[]  $mappers
     */
    public function __construct(ValueExtractorInterface $valueExtractor, $availableLanguages, array $mappers = array())
    {
        $this->valueExtractor = $valueExtractor;
        $this->availableLanguages = explode(',', $availableLanguages);
        $this->mappers = $mappers;
    }

    /**
     * @param FieldMapperInterface $mapper
     *
     * @return $this
     */
    public function addMapper(FieldMapperInterface $mapper)
    {
        $this->mappers[] = $mapper;

        return $this;
    }

    /**
     * @return array
     */
    public function getAvailableLanguages()
    {
        return $this->availableLanguages;
    }

    /**
     * @param NodeContext $node
     * @param string      $language
     * @param int         $version
     *
     * @return array
     */
    public function extract(NodeContext $node, $language, $version)
    {
        if (!$node->getContent()) {
            return array();
        }

        $content = $node->getContent($language, $version);
        $mappings = $node->getFieldMappings();

        if (!$mappings) {
            return array();
        }

        $titles = array();

        foreach ($mappings as $key => $mapping) {
            if ($mapper = $this->findFieldMapper($key)) {
                $title = $mapper->map($this->valueExtractor, $content, $mapping, $language);
                if ($title) {
                    $titles[$key] = $title;
                }
            }
        }

        if (empty($titles['backend'])) {
            $fallbackTitle = substr(get_class($content), strrpos(get_class($content), '\\') + 1);
            $titles['backend'] = "[$fallbackTitle, $version, $language]";
        }

        return $titles;
    }

    /**
     * @param string $key
     *
     * @return FieldMapperInterface|null
     */
    private function findFieldMapper($key)
    {
        foreach ($this->mappers as $mapper) {
            if ($mapper->accept($key)) {
                return $mapper;
            }
        }

        return null;
    }
}
