<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\MessageBundle\EventListener;

use Phlexible\Bundle\GuiBundle\Event\GetConfigEvent;
use Phlexible\Bundle\MessageBundle\Model\MessageManagerInterface;

/**
 * Get config listener
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class GetConfigListener
{
    /**
     * @var MessageManagerInterface
     */
    private $messageManager;

    /**
     * @param MessageManagerInterface $messageManager
     */
    public function __construct(MessageManagerInterface $messageManager)
    {
        $this->messageManager = $messageManager;
    }

    /**
     * @param GetConfigEvent $event
     */
    public function onGetConfig(GetConfigEvent $event)
    {
        $event->getConfig()
            ->set('message.priorities', $this->messageManager->getPriorityNames())
            ->set('message.types', $this->messageManager->getTypeNames());
    }
}
