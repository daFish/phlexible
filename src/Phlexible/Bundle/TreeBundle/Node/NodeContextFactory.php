<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\TreeBundle\Node;

use Phlexible\Bundle\TreeBundle\Entity\PartNode;
use Phlexible\Bundle\TreeBundle\Mediator\TreeMediatorInterface;
use Phlexible\Bundle\TreeBundle\Model\TreeInterface;
use Phlexible\Component\Node\Model\NodeInterface;

/**
 * Node context factory
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class NodeContextFactory implements NodeContextFactoryInterface
{
    /**
     * @var \Phlexible\Component\Node\Model\NodeInterface
     */
    protected $node;

    /**
     * @param TreeMediatorInterface $mediator
     */
    public function __construct(TreeMediatorInterface $mediator)
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
            $class = __NAMESPACE__ . '\TeaserContext';
        } else {
            $class = __NAMESPACE__ . '\NodeContext';
        }

        return new $class($node, $tree, $this->mediator, $language);
    }
}