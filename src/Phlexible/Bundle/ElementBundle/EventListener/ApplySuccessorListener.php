<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Bundle\ElementBundle\EventListener;

use Doctrine\DBAL\Connection;
use Phlexible\Bundle\UserBundle\Event\ApplySuccessorEvent;

/**
 * Apply successor listener.
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class ApplySuccessorListener
{
    /**
     * @var Connection
     */
    private $connection;

    /**
     * @param Connection $connection
     */
    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    /**
     * @param ApplySuccessorEvent $event
     */
    public function onApplySuccessor(ApplySuccessorEvent $event)
    {
        $fromUser = $event->getFromUser();
        $toUser = $event->getToUser();

        $fromUid = $fromUser->getId();
        $toUid = $toUser->getId();

        $this->connection->update(
            'element',
            array(
                'create_user_id' => $toUid,
            ),
            array(
                'create_user_id' => $fromUid,
            )
        );

        $this->connection->update(
            'element_history',
            array(
                'create_user_id' => $toUid,
            ),
            array(
                'create_user_id' => $fromUid,
            )
        );

        $this->connection->update(
            'element_version',
            array(
                'create_user_id' => $toUid,
            ),
            array(
                'create_user_id' => $fromUid,
            )
        );
    }
}
