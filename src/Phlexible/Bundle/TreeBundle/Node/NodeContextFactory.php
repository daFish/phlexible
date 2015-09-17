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

use Phlexible\Bundle\TreeBundle\Entity\PartNode;
use Phlexible\Bundle\TreeBundle\Mediator\MediatorInterface;
use Phlexible\Bundle\TreeBundle\Model\TreeInterface;
use Phlexible\Component\Node\Model\NodeInterface;

/**
 * Node context factory.
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class NodeContextFactory implements NodeContextFactoryInterface
{
    /**
     * @var NodeInterface
     */
    protected $node;

    /**
     * @param MediatorInterface $mediator
     */
    public function __construct(MediatorInterface $mediator)
    {
        $this->mediator = $mediator;
    }

    /**
     * @param TreeInterface $tree
     * @param NodeInterface $node
     * @param string        $language
     *
     * @return NodeContext
     */
    public function factory(TreeInterface $tree, NodeInterface $node, $language)
    {
        if ($node instanceof PartNode) {
            $class = __NAMESPACE__.'\TeaserContext';
        } else {
            $class = __NAMESPACE__.'\NodeContext';
        }

        return new $class($node, $tree, $this->mediator, $language);
    }
}
