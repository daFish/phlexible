<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\TeaserBundle\Area;

use Phlexible\Bundle\TeaserBundle\Entity\Teaser;
use Phlexible\Bundle\TeaserBundle\Teaser\TeaserContext;
use Phlexible\Bundle\TreeBundle\Node\NodeContext;
use Phlexible\Component\Node\Model\NodeManagerInterface;

/**
 * Area manager
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
     * @return Teaser[]
     */
    public function findCascadingByAreaAndNode($area, NodeContext $forNode, $includeLocalHidden = true)
    {
        /* @var $teasers Teaser[] */
        $teasers = array();
        $forNodeId = $forNode->getId();

        foreach ($forNode->getPath() as $node) {
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
     * @return Teaser[]
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
