<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\ElementtypeBundle\EventListener;

use Doctrine\DBAL\Connection;
use Phlexible\Bundle\UserBundle\Event\ApplySuccessorEvent;

/**
 * Elementtypes listeners
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
            'elementtype',
            array(
                'create_user_id' => $toUid,
            ),
            array(
                'create_user_id' => $fromUid
            )
        );

        $this->connection->update(
            'elementtype_version',
            array(
                'create_user_id' => $toUid,
            ),
            array(
                'create_user_id' => $fromUid
            )
        );
    }
}
