<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\UserBundle\Model;

use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\Query\Expr;

/**
 * User query interface
 *
 * @author  Stephan Wentz <sw@brainbits.net>
 */
interface UserQueryInterface
{
    /**
     * Return users
     *
     * @param Criteria $criteria
     *
     * @return \Countable|\Iterator
     */
    public function getResult(Criteria $criteria);
}