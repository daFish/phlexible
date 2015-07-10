<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\TeaserBundle\Event;

use Symfony\Component\EventDispatcher\Event;

/**
 * Before stop inherit inherited teaser event
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class BeforeStopInheritInheritedTeaserEvent extends Event
{
    /**
     * @var int
     */
    private $nodeId;

    /**
     * @var int
     */
    private $eid;

    /**
     * @var int
     */
    private $teaserId;

    /**
     * @var int
     */
    private $layoutareaId;

    /**
     * @param int $nodeId
     * @param int $eid
     * @param int $teaserId
     * @param int $layoutareaId
     */
    public function __construct($nodeId, $eid, $teaserId, $layoutareaId)
    {
        $this->nodeId = $nodeId;
        $this->eid = $eid;
        $this->teaserId = $teaserId;
        $this->layoutareaId = $layoutareaId;
    }

    /**
     * @return int
     */
    public function getNodeId()
    {
        return $this->nodeId;
    }

    /**
     * @return int
     */
    public function getEid()
    {
        return $this->eid;
    }

    /**
     * @return int
     */
    public function getTeaserId()
    {
        return $this->teaserId;
    }

    /**
     * @return int
     */
    public function getLayoutareaId()
    {
        return $this->layoutareaId;
    }
}
