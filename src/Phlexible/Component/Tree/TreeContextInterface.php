<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Component\Tree;

/**
 * Tree context interface
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
interface TreeContextInterface
{
    /**
     * @return string
     */
    public function getWorkspace();

    /**
     * @return string
     */
    public function getLocale();
}
