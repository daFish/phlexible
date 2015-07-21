<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\TreeBundle\Model;

use Phlexible\Bundle\TreeBundle\Entity\NodeState;

/**
 * Node state manager interface
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
interface NodeStateManagerInterface
{
    /**
     * @param array $criteria
     *
     * @return NodeState[]
     */
    public function findBy(array $criteria);

    /**
     * @param array $criteria
     *
     * @return NodeState
     */
    public function findOneBy(array $criteria);

    /**
     * @param NodeState $nodeOnline
     */
    public function updateState(NodeState $nodeOnline);

    /**
     * @param NodeState $nodeOnline
     */
    public function deleteState(NodeState $nodeOnline);
}
