<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Bundle\DataSourceBundle\EventListener;

use Doctrine\ORM\EntityManager;
use Phlexible\Bundle\UserBundle\Event\ApplySuccessorEvent;

/**
 * Apply successor listener
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class ApplySuccessorListener
{
    /**
     * @var EntityManager
     */
    private $entityManager;

    /**
     * @param EntityManager $entityManager
     */
    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * User delete callback
     * Will be called after a user is deleted to cleanup uids
     *
     * @param ApplySuccessorEvent $event
     */
    public function onApplySuccessor(ApplySuccessorEvent $event)
    {
        $datasourceRepository = $this->entityManager->getRepository('PhlexibleDataSourceBundle:DataSource');

        $fromUser = $event->getFromUser();
        $toUser = $event->getToUser();

        $fromUserId = $fromUser->getId();
        $toUserId = $toUser->getId();

        foreach ($datasourceRepository->findByCreateUserId($fromUserId) as $datasource) {
            $datasource->setCreateUserId($toUserId);
        }

        foreach ($datasourceRepository->findByModifyUserId($fromUserId) as $datasource) {
            $datasource->setModifyUserId($toUserId);
        }

        $this->entityManager->flush();
    }
}
