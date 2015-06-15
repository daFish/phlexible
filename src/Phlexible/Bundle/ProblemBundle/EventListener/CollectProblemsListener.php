<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Bundle\ProblemBundle\EventListener;

use Phlexible\Bundle\GuiBundle\Properties\Properties;
use Phlexible\Bundle\ProblemBundle\Entity\Problem;
use Phlexible\Bundle\ProblemBundle\Event\CollectProblemsEvent;

/**
 * Collect problems listener
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class CollectProblemsListener
{
    /**
     * @var Properties
     */
    private $properties;

    /**
     * @param Properties $properties
     */
    public function __construct(Properties $properties)
    {
        $this->properties = $properties;
    }

    /**
     * @param CollectProblemsEvent $event
     */
    public function onCollectProblems(CollectProblemsEvent $event)
    {
        $lastRun = $this->properties->get('problems', 'last_run');

        if (!$lastRun) {
            $problem = new Problem();
            $problem
                ->setId('problem_check_no_run')
                ->setSeverity(Problem::SEVERITY_WARNING)
                ->setMessage('Cached problems check was never run.')
                ->setHint('Run cached problem check command')
                ->setIconClass('p-problem-component-icon')
                ->setCreatedAt(new \DateTime())
                ->setLastCheckedAt(new \DateTime());

            $event->addProblem($problem);
        } elseif (time() - strtotime($lastRun) > 86400) {
            $problem = new Problem();
            $problem
                ->setId('problem_check_long_ago')
                ->setSeverity(Problem::SEVERITY_WARNING)
                ->setMessage('Cached problems last check run was on "' . $lastRun . '", more than 24h ago.')
                ->setHint('Install a cronjob for running the cached problem check command')
                ->setIconClass('p-problem-component-icon')
                ->setCreatedAt(new \DateTime())
                ->setLastCheckedAt(new \DateTime());

            $event->addProblem($problem);
        }
    }
}
