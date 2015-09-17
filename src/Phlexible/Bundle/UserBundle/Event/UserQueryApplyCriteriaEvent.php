<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Bundle\UserBundle\Event;

use Doctrine\Common\Collections\Criteria;
use Symfony\Component\EventDispatcher\Event;

/**
 * User query apply criteria event.
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
     * Return criteria.
     *
     * @return Criteria
     */
    public function getCriteria()
    {
        return $this->criteria;
    }
}
