<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Component\MessageSubscription\Doctrine;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Phlexible\Component\MessageSubscription\Domain\Subscription;
use Phlexible\Component\MessageSubscription\Model\SubscriptionManagerInterface;

/**
 * Doctrine subscription manager.
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class SubscriptionManager implements SubscriptionManagerInterface
{
    /**
     * @var EntityManager
     */
    private $entityManager;

    /**
     * @var EntityRepository
     */
    private $subscriptionRepository;

    /**
     * @param EntityManager $entityManager
     */
    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;

        $this->subscriptionRepository = $entityManager->getRepository('Phlexible\Component\MessageSubscription\Domain\Subscription');
    }

    /**
     * {@inheritdoc}
     */
    public function create()
    {
        return new Subscription();
    }

    /**
     * {@inheritdoc}
     */
    public function find($id)
    {
        return $this->subscriptionRepository->find($id);
    }

    /**
     * {@inheritdoc}
     */
    public function findAll()
    {
        return $this->subscriptionRepository->findAll();
    }

    /**
     * {@inheritdoc}
     */
    public function findBy(array $criteria, $orderBy = null, $limit = null, $offset = null)
    {
        return $this->subscriptionRepository->findBy($criteria, $orderBy, $limit, $offset);
    }

    /**
     * {@inheritdoc}
     */
    public function findOneBy(array $criteria, $orderBy = null)
    {
        return $this->subscriptionRepository->findOneBy($criteria, $orderBy);
    }

    /**
     * {@inheritdoc}
     */
    public function updateSubscription(Subscription $subscription)
    {
        $this->entityManager->persist($subscription);
        $this->entityManager->flush($subscription);
    }

    /**
     * {@inheritdoc}
     */
    public function deleteSubscription(Subscription $subscription)
    {
        $this->entityManager->remove($subscription);
        $this->entityManager->flush();
    }
}
