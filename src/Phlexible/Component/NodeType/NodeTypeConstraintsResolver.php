<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Component\NodeType;

use Phlexible\Bundle\TreeBundle\Node\NodeContext;
use Phlexible\Component\NodeType\Model\NodeTypeConstraintsResolverInterface;
use Phlexible\Component\Site\Model\SiteManagerInterface;

/**
 * Node type constraint resolver
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class NodeTypeConstraintsResolver implements NodeTypeConstraintsResolverInterface
{
    /**
     * @var SiteManagerInterface
     */
    private $siteManager;

    /**
     * @param SiteManagerInterface $siteManager
     */
    public function __construct(SiteManagerInterface $siteManager)
    {
        $this->siteManager = $siteManager;
    }

    /**
     * @param NodeContext $node
     *
     * @return array
     */
    public function resolve(NodeContext $node)
    {
        $site = $this->siteManager->find($node->getSiterootId());

        $constraints = $site->getNodeConstraints();

        $nodeTypes = array();
        if (isset($constraints[$node->getContentType()])) {
            $constraint = $constraints[$node->getContentType()];

            foreach ($constraint->getNodeTypes() as $nodeType) {
                if (!isset($constraints[$nodeType]) || !$constraints[$nodeType]->isAllowed()) {
                    continue;
                }
                $nodeTypes[] = $nodeType;
            }
        }

        return $nodeTypes;
    }
}
