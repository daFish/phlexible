<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\ElementBundle\Proxy\Distiller;

/**
 * Distilled node collection
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class DistilledNodeCollection implements \Countable
{
    /**
     * @var DistilledNodeInterface[]
     */
    private $nodes;

    /**
     * @param DistilledNodeInterface[] $nodes
     */
    public function __construct(array $nodes)
    {
        $this->nodes = $nodes;
    }

    /**
     * @return DistilledNodeInterface[]
     */
    public function all()
    {
        return $this->nodes;
    }

    /**
     * @param self $nodes
     *
     * @return self
     */
    public function merge(self $nodes)
    {
        $mergedNodes = $this->nodes;

        foreach ($nodes as $node) {
            $mergedNodes[] = $node;
        }

        return new self($mergedNodes);
    }

    /**
     * @param callable $callback
     *
     * @return self
     */
    public function filter(callable $callback)
    {
        $filteredNodes = array();

        foreach ($this->nodes as $node) {
            if ($callback($node)) {
                $filteredNodes[] = $node;
            }
        }

        return new self($filteredNodes);
    }

    /**
     * {@inheritdoc}
     */
    public function count()
    {
        return count($this->nodes);
    }
}
