<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
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
