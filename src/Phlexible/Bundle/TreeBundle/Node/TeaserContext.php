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
