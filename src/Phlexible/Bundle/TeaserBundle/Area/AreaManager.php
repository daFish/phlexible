<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Bundle\TeaserBundle\Area;

use Phlexible\Bundle\TreeBundle\Entity\PartNode;
use Phlexible\Bundle\TreeBundle\Node\NodeContext;
use Phlexible\Component\Node\Model\NodeManagerInterface;

/**
 * Area manager.
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class AreaManager
{
    /**
     * @var \Phlexible\Component\Node\Model\NodeManagerInterface
     */
    private $nodeManager;

    /**
     * @param NodeManagerInterface $nodeManager
     */
    public function __construct(NodeManagerInterface $nodeManager)
    {
        $this->nodeManager = $nodeManager;
    }

    /**
     * @param mixed       $area
     * @param NodeContext $forNode
     * @param bool        $includeLocalHidden
     *
     * @return PartNode[]
     */
    public function findCascadingByAreaAndNode($area, NodeContext $forNode, $includeLocalHidden = true)
    {
        $teasers = array();
        $forNodeId = $forNode->getId();

        foreach ($forNode->getNodePath() as $node) {
            foreach ($this->findByAreaAndNode($area, $node) as $teaser) {
                if ($node->getId() !== $forNodeId && $teaser->hasStopId($node->getId())) {
                    continue;
                }
                if ($teaser->hasStopId($forNodeId)) {
                    $teaser->setStopped();
                }
                if ($teaser->hasHideId($forNodeId)) {
                    $teaser->setHidden();
                }

                $teasers[$teaser->getId()] = $teaser;
            }

            if ($node->getId() !== $forNodeId) {
                foreach ($teasers as $index => $teaser) {
                    if ($teaser->hasStopId($forNode->getId())) {
                        unset($teasers[$index]);
                    }
                }
            } elseif (!$includeLocalHidden) {
                foreach ($teasers as $index => $teaser) {
                    if ($teaser->isHidden($forNode)) {
                        unset($teasers[$index]);
                    }
                }
            }
        }

        return $teasers;
    }

    /**
     * @param mixed       $area
     * @param NodeContext $node
     *
     * @return PartNod[]
     */
    public function findByAreaAndNode($area, NodeContext $node)
    {
        $teasers = $node->getTree()->getChildren(
            $node,
            array('Phlexible\Bundle\TreeBundle\Entity\PartNode')
        );

        foreach ($teasers as $index => $teaser) {
            if ($teaser->getAreaId() !== $area->getId()) {
                unset($teasers[$index]);
            }
        }

        return $teasers;
    }
}
