<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Bundle\TreeBundle\Node;

/**
 * Node hasher.
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class NodeHasher implements NodeHasherInterface
{
    /**
     * @var string
     */
    private $algo;

    /**
     * @var array
     */
    private $hashes = array();

    /**
     * @param string $algo
     */
    public function __construct($algo = 'md5')
    {
        $this->algo = $algo;
    }

    /**
     * @param NodeContext $node
     * @param int         $version
     * @param string      $language
     *
     * @return string
     */
    public function hashNode(NodeContext $node, $version, $language)
    {
        $identifier = "{$node->getId()}__{$version}__{$language}";

        if (isset($this->hashes[$identifier])) {
            return $this->hashes[$identifier];
        }

        $hash = $this->hashValues($this->createHashValuesByNode($node, $version, $language));

        $this->hashes[$identifier] = $hash;

        return $hash;
    }

    /**
     * @param NodeContext $node
     * @param int         $version
     * @param string      $language
     *
     * @return array
     */
    private function createHashValuesByNode(NodeContext $node, $version, $language)
    {
        $values = $node->getAttributes();
        $values['navigation'] = $node->getInNavigation();
        $values['content'] = $node->getContent($language, $version);

        return $values;
    }

    /**
     * @param array $values
     *
     * @return string
     */
    private function hashValues(array $values)
    {
        return hash($this->algo, serialize($values));
    }
}
