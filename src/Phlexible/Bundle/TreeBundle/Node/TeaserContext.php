<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\TreeBundle\Node;

/**
 * Teaser context
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class TeaserContext extends NodeContext
{
    /**
     * {@inheritdoc}
     */
    public function getAreaId()
    {
        return $this->node->getAreaId();
    }

    /**
     * @return array
     */
    public function getStopIds()
    {
        return $this->node->getStopIds();
    }

    /**
     * @param int $stopId
     *
     * @return bool
     */
    public function hasStopId($stopId)
    {
        return $this->node->hasStopId($stopId);
    }

    /**
     * @return array
     */
    public function getHideIds()
    {
        return $this->node->getHideIds();
    }

    /**
     * @param int $hideId
     *
     * @return bool
     */
    public function hasHideId($hideId)
    {
        $this->node->hasHideId($hideId);
    }

    /**
     * @param NodeContext $node
     *
     * @return int
     */
    public function isHidden(NodeContext $node)
    {
        return $this->node->hasHideId($node->getId());
    }

    /**
     * @param NodeContext $node
     *
     * @return int
     */
    public function isStopped(NodeContext $node)
    {
        return $this->node->hasStopId($node->getId());
    }
}
