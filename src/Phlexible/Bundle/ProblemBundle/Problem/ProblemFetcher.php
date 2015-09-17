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

use Doctrine\ORM\EntityManager;
use Phlexible\Bundle\ProblemBundle\Event\CollectProblemsEvent;
use Phlexible\Bundle\ProblemBundle\ProblemEvents;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Problems fetcher.
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class ProblemFetcher implements ProblemFetcherInterface
{
    /**
     * @var EntityManager
     */
    private $entityManager;

    /**
     * @var EventDispatcherInterface
     */
    private $dispatcher;

    /**
     * @param EntityManager            $entityManager
     * @param EventDispatcherInterface $dispatcher
     */
    public function __construct(EntityManager $entityManager, EventDispatcherInterface $dispatcher)
    {
        $this->entityManager = $entityManager;
        $this->dispatcher = $dispatcher;
    }

    /**
     * {@inheritdoc}
     */
    public function fetch()
    {
        $event = new CollectProblemsEvent();
        $this->dispatcher->dispatch(ProblemEvents::COLLECT, $event);

        $problemsRepository = $this->entityManager->getRepository('PhlexibleProblemBundle:Problem');

        $liveProblems = $event->getProblems();
        foreach ($liveProblems as $liveProblem) {
            $liveProblem->setLive(true);
        }
        $cachedProblems = $problemsRepository->findAll();

        return $liveProblems + $cachedProblems;
    }
}
