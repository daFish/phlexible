<?php
/**
 * phlexible
 *
 * @copyright 2007 brainbits GmbH (http://www.brainbits.net)
 * @license   http://www.makeweb.de/LICENCE     Dummy Licence
 */

namespace Phlexible\Bundle\UserBundle\Event;

use Doctrine\Common\Collections\Criteria;
use Symfony\Component\EventDispatcher\Event;

/**
 * User query apply criteria event
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class UserQueryApplyCriteriaEvent extends Event
{
    /**
     * @var Criteria
     */
    private $criteria;

    /**
     * Constructor.
     *
     * @param Criteria $criteria
     */
    public function __construct(Criteria $criteria)
    {
        $this->criteria = $criteria;
    }

    /**
     * Return criteria
     *
     * @return Criteria
     */
    public function getCriteria()
    {
        return $this->criteria;
    }
}
