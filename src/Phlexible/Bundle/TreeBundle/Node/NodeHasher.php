<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\TreeBundle\Node;

use Phlexible\Bundle\ElementBundle\Element\ElementHasher;
use Phlexible\Bundle\TreeBundle\Model\NodeInterface;

/**
 * Node hasher
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class NodeHasher
{
    /**
     * @var ElementHasher
     */
    private $elementHasher;

    /**
     * @var string
     */
    private $algo;

    /**
     * @var array
     */
    private $hashes = array();

    /**
     * @param ElementHasher $elementHasher
     * @param string        $algo
     */
    public function __construct(ElementHasher $elementHasher, $algo = 'md5')
    {
        $this->elementHasher = $elementHasher;
        $this->algo = $algo;
    }

    /**
     * @param NodeInterface $node
     * @param int               $version
     * @param string            $language
     *
     * @return string
     */
    public function hashNode(NodeInterface $node, $version, $language)
    {
        $identifier = "{$node->getId()}__{$version}__{$language}";

        if (isset($this->hashes[$identifier])) {
            return $this->hashes[$identifier];
        }

        $values = $this->createHashValuesByNode($node, $version, $language);
        $hash = $this->hashValues($values);

        $this->hashes[$identifier] = $hash;

        return $hash;
    }

    /**
     * @param NodeInterface $node
     * @param int               $version
     * @param string            $language
     *
     * @return array
     */
    private function createHashValuesByNode(NodeInterface $node, $version, $language)
    {
        $eid = $node->getTypeId();

        $attributes = $node->getAttributes();
        $attributes['navigation'] = $node->getInNavigation();

        $values = $this->elementHasher->createHashValuesByEid($eid, $version, $language);
        $values['attributes'] = $attributes;

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
