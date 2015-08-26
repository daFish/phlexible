<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Component\NodeType\Model;

/**
 * Node type provider interface
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
interface NodeTypeProviderInterface
{
    /**
     * @return array
     */
    public function getTypes();
}
