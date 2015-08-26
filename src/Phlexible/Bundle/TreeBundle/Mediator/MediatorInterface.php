<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Bundle\TreeBundle\Mediator;

use Phlexible\Bundle\TreeBundle\Node\NodeContext;

/**
 * Mediator interface
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
interface MediatorInterface
{
    /**
     * @param NodeContext $node
     *
     * @return bool
     */
    public function accept(NodeContext $node);
}
