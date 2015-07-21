<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\TreeBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Phlexible\Bundle\TreeBundle\Model\PartInterface;

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
}
