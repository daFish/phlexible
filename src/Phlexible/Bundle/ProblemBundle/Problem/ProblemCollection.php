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
 * Problem collection
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class ProblemCollection implements \IteratorAggregate, \Countable
{
    /**
     * @var Problem[]
     */
    private $problems = array();

    /**
     * @param Problem[] $problems
     */
    public function __construct(array $problems = array())
    {
        foreach ($problems as $problem) {
            $this->add($problem);
        }
    }

    /**
     * @param Problem $problem
     *
     * @return $this
     */
    public function add(Problem $problem)
    {
        $this->problems[$problem->getId()] = $problem;

        return $this;
    }

    /**
     * @param Problem $problem
     *
     * @return bool
     */
    public function contains(Problem $problem)
    {
        return isset($this->problems[$problem->getId()]);
    }

    /**
     * @param Problem $problem
     */
    public function remove(Problem $problem)
    {
        if ($this->contains($problem)) {
            unset($this->problems[$problem->getId()]);
        }
    }

    /**
     * @param ProblemCollection $problems
     *
     * @return ProblemCollection
     */
    public function merge(ProblemCollection $problems)
    {
        $merge = new self($this->problems);

        foreach ($problems as $problem) {
            $merge->add($problem);
        }

        return $merge;
    }

    /**
     * @param ProblemCollection $problems
     *
     * @return ProblemCollection
     */
    public function intersect(ProblemCollection $problems)
    {
        $intersect = new self();

        foreach ($problems as $problem) {
            if ($this->contains($problem)) {
                $intersect->add($problem);
            }
        }

        return $intersect;
    }

    /**
     * @param ProblemCollection $problems
     *
     * @return ProblemCollection
     */
    public function diff(ProblemCollection $problems)
    {
        $diff = new self();

        foreach ($problems as $problem) {
            if (!$this->contains($problem)) {
                $diff->add($problem);
            }
        }

        return $diff;
    }

    /**
     * @return \ArrayIterator
     */
    public function getIterator()
    {
        return new \ArrayIterator($this->problems);
    }

    /**
     * @return int
     */
    public function count()
    {
        return count($this->problems);
    }
}
