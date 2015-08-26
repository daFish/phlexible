<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Component\NodeType;

use Phlexible\Bundle\TreeBundle\Node\NodeContext;
use Phlexible\Component\NodeType\Model\NodeTypeConstraintsResolverInterface;
use Phlexible\Component\NodeType\Model\NodeTypeManagerInterface;
use Phlexible\Component\NodeType\Model\NodeTypeProviderInterface;

/**
 * Node type manager
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class NodeTypeManager implements NodeTypeManagerInterface
{
    /**
     * @var NodeTypeProviderInterface
     */
    private $nodeTypeProvider;

    /**
     * @var NodeTypeConstraintsResolverInterface
     */
    private $constraintResolver;

    /**
     * @param NodeTypeProviderInterface            $nodeTypeProvider
     * @param NodeTypeConstraintsResolverInterface $constraintResolver
     */
    public function __construct(
        NodeTypeProviderInterface $nodeTypeProvider,
        NodeTypeConstraintsResolverInterface $constraintResolver
    ) {
        $this->nodeTypeProvider = $nodeTypeProvider;
        $this->constraintResolver = $constraintResolver;
    }

    /**
     * {@inheritdoc}
     */
    public function getTypes()
    {
        return $this->nodeTypeProvider->getTypes();
    }

    /**
     * {@inheritdoc}
     */
    public function getTypesForNode(NodeContext $node)
    {
        $types = $this->getTypes();

        return array_intersect_key($types, array_flip($this->constraintResolver->resolve($node)));
    }
}
