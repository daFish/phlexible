<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\MessageBundle\Model;

use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\Query\Expr;

/**
 * Message query interface
 *
 * @author  Stephan Wentz <sw@brainbits.net>
 */
interface MessageQueryInterface
{
    /**
     * Return users
     *
     * @param Criteria $criteria
     *
     * @return \Countable|\Iterator
     */
    public function getResult(Criteria $criteria);

    /**
     * @param Criteria $criteria
     *
     * @return array
     */
    public function getFacets(Criteria $criteria);
}