<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Bundle\ProblemBundle\Problem;

use Phlexible\Bundle\ProblemBundle\Entity\Problem;

/**
 * Problems fetcher interface
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
interface ProblemFetcherInterface
{
    /**
     * @return Problem[]
     */
    public function fetch();
}
