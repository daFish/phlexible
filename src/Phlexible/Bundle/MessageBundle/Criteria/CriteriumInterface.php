<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\MessageBundle\Criteria;

/**
 * Message criterium interface
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
interface CriteriumInterface
{
    /**
     * @return string
     */
    public function getType();

    /**
     * @return mixed
     */
    public function getValue();

    /**
     * @return array
     */
    public function toArray();
}