<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\TreeBundle\Mediator\VersionStrategy;

use Phlexible\Bundle\ElementBundle\Entity\ElementVersion;
use Phlexible\Bundle\TreeBundle\Node\NodeContext;

/**
 * Version strategy interface
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
interface VersionStrategyInterface
{
    /**
     * @param NodeContext $node
     * @param string      $language
     *
     * @return ElementVersion
     */
    public function findElementVersion(NodeContext $node, $language);
}