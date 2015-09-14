<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Bundle\TreeBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Phlexible\Bundle\TreeBundle\Model\PartInterface;
use Phlexible\Component\Node\Domain\Node;

/**
 * Part node
 *
 * @author Stephan Wentz <sw@brainbits.net>
 *
 * @ORM\Entity
 */
class PartNode extends Node implements PartInterface
{
    /**
     * @var string
     * @ORM\Column(name="area_id", type="string", length=36, options={"fixed": true})
     */
    private $areaId;

    /**
     * {@inheritdoc}
     */
    public function getAreaId()
    {
        return $this->areaId;
    }

    /**
     * {@inheritdoc}
     */
    public function setAreaId($areaId)
    {
        $this->areaId = $areaId;

        return $this;
    }

    /**
     * @return array
     */
    public function getStopIds()
    {
        return $this->getAttribute('stopIds', array());
    }

    /**
     * @param array $stopIds
     *
     * @return $this
     */
    public function setStopIds($stopIds = array())
    {
        $this->setAttribute('stopIds', $stopIds);

        return $this;
    }

    /**
     * @param int $stopId
     *
     * @return $this
     */
    public function addStopId($stopId)
    {
        $stopIds = $this->getStopIds();

        if (!in_array($stopId, $stopIds)) {
            $stopIds[] = $stopId;
            $this->setStopIds($stopIds);
        }

        return $this;
    }

    /**
     * @param int $stopId
     *
     * @return $this
     */
    public function removeStopId($stopId)
    {
        $stopIds = $this->getStopIds();

        if (in_array($stopId, $stopIds)) {
            unset($stopIds[array_search($stopId, $stopIds)]);
            $this->setStopIds($stopIds);
        }

        return $this;
    }

    /**
     * @param int $stopId
     *
     * @return bool
     */
    public function hasStopId($stopId)
    {
        $stopIds = $this->getStopIds();

        return in_array($stopId, $stopIds);
    }

    /**
     * @return array
     */
    public function getHideIds()
    {
        return $this->getAttribute('hideIds', array());
    }

    /**
     * @param array $hideIds
     *
     * @return $this
     */
    public function setHideIds($hideIds)
    {
        return $this->setAttribute('hideIds', $hideIds);
    }

    /**
     * @param int $hideId
     *
     * @return $this
     */
    public function addHideId($hideId)
    {
        $hideIds = $this->getHideIds();

        if (!in_array($hideId, $hideIds)) {
            $hideIds[] = $hideId;
            $this->setHideIds($hideIds);
        }

        return $this;
    }

    /**
     * @param int $hideId
     *
     * @return $this
     */
    public function removeHideId($hideId)
    {
        $hideIds = $this->getHideIds();

        if (in_array($hideId, $hideIds)) {
            unset($hideIds[array_search($hideId, $hideIds)]);
            $this->setHideIds($hideIds);
        }

        return $this;
    }

    /**
     * @param int $hideId
     *
     * @return bool
     */
    public function hasHideId($hideId)
    {
        $hideIds = $this->getHideIds();

        return in_array($hideId, $hideIds);
    }
}
