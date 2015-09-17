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

use Phlexible\Bundle\ProblemBundle\Domain\ProblemCheckerCollection;

/**
 * Problem collector.
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class ProblemCollector
{
    /**
     * @var ProblemCheckerCollection
     */
    private $problemCheckers;

    /**
     * @param ProblemCheckerCollection $problemCheckers
     */
    public function __construct(ProblemCheckerCollection $problemCheckers)
    {
        $this->problemCheckers = $problemCheckers;
    }

    /**
     * @return ProblemCollection
     */
    public function collect()
    {
        $problems = new ProblemCollection();

        foreach ($this->problemCheckers as $problemChecker) {
            $problems->merge($problemChecker->check());
        }

        return $problems;
    }
}
